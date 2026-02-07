<?php
/**
 * Mastodon 动态
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$mastodonTitle = trim((string) clarity_opt('mastodon_title', '动态'));
$mastodonDesc = trim((string) clarity_opt('mastodon_desc', '来自 Mastodon 的碎碎念'));
$mastodonInstance = trim((string) clarity_opt('mastodon_instance', ''));
$mastodonUserId = trim((string) clarity_opt('mastodon_userid', ''));
$mastodonToken = trim((string) clarity_opt('mastodon_token', ''));

// 获取博客作者信息
$blogAuthor = null;
$blogAvatar = '';
$blogAuthorName = '';
try {
    $options = Typecho_Widget::widget('Widget_Options');
    $db = Typecho_Db::get();
    $author = $db->fetchRow($db->select()->from('table.users')->where('uid = ?', 1));
    if ($author) {
        $blogAuthorName = $author['screenName'] ?: $author['name'];
        $blogAvatar = $author['avatar'] ?: '';
        if (empty($blogAvatar)) {
            // 使用默认头像或 Gravatar
            $blogAvatar = 'https://cn.cravatar.com/avatar/' . md5(strtolower($author['mail'] ?? '')) . '?s=100&d=mp';
        }
    }
} catch (Exception $e) {
    // 忽略错误
}

clarity_set('showAside', true);
clarity_set('pageTitle', $mastodonTitle);
clarity_set('isLinksPage', false);

// 获取 Mastodon 数据
$toots = [];
if ($mastodonInstance && $mastodonUserId) {
    $cacheFile = __DIR__ . '/cache/mastodon_' . md5($mastodonUserId) . '.json';
    $cacheTime = 300; // 5分钟缓存
    
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        $toots = json_decode(file_get_contents($cacheFile), true) ?: [];
    } else {
        $apiUrl = rtrim($mastodonInstance, '/') . '/api/v1/accounts/' . $mastodonUserId . '/statuses?limit=40';
        $headers = [];
        if ($mastodonToken) {
            $headers[] = 'Authorization: Bearer ' . $mastodonToken;
        }
        
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if (is_array($data)) {
                $toots = $data;
                // 缓存数据
                if (!is_dir(__DIR__ . '/cache')) {
                    mkdir(__DIR__ . '/cache', 0755, true);
                }
                file_put_contents($cacheFile, json_encode($toots));
            }
        }
    }
}

// 过滤回复和转发
$filteredToots = [];
foreach ($toots as $toot) {
    // 跳过回复和纯转发
    if (!empty($toot['in_reply_to_id']) || (!empty($toot['reblog']) && empty($toot['content']))) {
        continue;
    }
    
    // 处理转发
    if (!empty($toot['reblog'])) {
        $toot['is_reblog'] = true;
        $toot['reblog_author'] = $toot['reblog']['account']['display_name'] ?: $toot['reblog']['account']['username'];
        $toot['reblog_avatar'] = $toot['reblog']['account']['avatar'];
        $toot['content'] = $toot['reblog']['content'];
        $toot['media_attachments'] = $toot['reblog']['media_attachments'];
    }
    
    $filteredToots[] = $toot;
}

?>
<?php $this->need('header.php'); ?>

<div class="page-header mastodon-header">
  <h1 class="page-title text-creative">
    <span class="icon-[ph--mastodon-logo-bold]"></span>
    <span><?php echo htmlspecialchars($mastodonTitle, ENT_QUOTES, 'UTF-8'); ?></span>
  </h1>
  <p class="page-desc"><?php echo htmlspecialchars($mastodonDesc, ENT_QUOTES, 'UTF-8'); ?></p>
  <a href="<?php echo $mastodonInstance ? htmlspecialchars(rtrim($mastodonInstance, '/') . '/@' . ($toots[0]['account']['username'] ?? ''), ENT_QUOTES, 'UTF-8') : '#'; ?>" class="mastodon-link" title="访问 Mastodon" target="_blank" rel="noopener">
    <span class="icon-[ph--arrow-square-out-bold]"></span>
    访问主页
  </a>
</div>

<div class="moments-list proper-height">
  <?php if (empty($filteredToots)): ?>
    <div class="moments-empty">
      <span class="icon-[ph--mastodon-logo-bold]"></span>
      <p>暂无动态</p>
      <?php if (!$mastodonInstance || !$mastodonUserId): ?>
        <p class="moments-hint">请在主题设置中配置 Mastodon 实例地址和用户 ID</p>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <?php foreach ($filteredToots as $idx => $toot): 
      $createdAt = strtotime($toot['created_at']);
      $timeDisplay = date('Y-m-d', $createdAt);
      $timeTitle = date('Y-m-d H:i', $createdAt);
      $content = $toot['content'];
      // 处理内容中的链接
      $content = preg_replace('/<a href="([^"]+)"[^>]*>([^<]+)<\/a>/', '<a href="$1" target="_blank" rel="noopener">$2</a>', $content);
      $media = $toot['media_attachments'] ?? [];
      $mediaCount = count($media);
      $mediaClass = $mediaCount === 1 ? 'single' : ($mediaCount === 2 ? 'double' : 'grid');
    ?>
      <article class="moment-card" id="toot-<?php echo $toot['id']; ?>" style="--delay: <?php echo ($idx * 0.05); ?>s">
        <header class="moment-header">
          <div class="author-info">
            <?php if (!empty($toot['is_reblog'])): ?>
              <img src="<?php echo htmlspecialchars($blogAvatar, ENT_QUOTES, 'UTF-8'); ?>" alt="" class="author-avatar" loading="lazy" />
              <span class="author-name"><?php echo htmlspecialchars($blogAuthorName, ENT_QUOTES, 'UTF-8'); ?></span>
              <span class="reblog-badge">
                <span class="icon-[ph--repeat-bold]"></span>
                转发
              </span>
            <?php else: ?>
              <img src="<?php echo htmlspecialchars($blogAvatar, ENT_QUOTES, 'UTF-8'); ?>" alt="" class="author-avatar" loading="lazy" />
              <span class="author-name"><?php echo htmlspecialchars($blogAuthorName, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endif; ?>
          </div>
          <time class="moment-time" datetime="<?php echo $toot['created_at']; ?>" title="<?php echo $timeTitle; ?>">
            <span class="icon-[ph--calendar-dots-bold]"></span>
            <span><?php echo $timeDisplay; ?></span>
          </time>
        </header>

        <div class="moment-body">
          <?php if ($content): ?>
            <div class="moment-text"><?php echo $content; ?></div>
          <?php endif; ?>

          <?php if ($mediaCount > 0): ?>
            <div class="moment-media <?php echo $mediaClass; ?>">
              <?php foreach ($media as $item): ?>
                <?php if ($item['type'] === 'video' || $item['type'] === 'gifv'): ?>
                  <figure class="media-item video">
                    <video src="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" controls preload="metadata" poster="<?php echo htmlspecialchars($item['preview_url'], ENT_QUOTES, 'UTF-8'); ?>"></video>
                  </figure>
                <?php elseif ($item['type'] === 'audio'): ?>
                  <figure class="media-item audio">
                    <audio src="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" controls></audio>
                  </figure>
                <?php else: ?>
                  <figure class="media-item photo">
                    <img src="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy" />
                  </figure>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <footer class="moment-footer">
          <div class="moment-actions">
            <a class="action-btn reply" href="<?php echo htmlspecialchars($toot['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" title="回复">
              <span class="icon-[ph--chat-circle-dots-bold]"></span>
              <span><?php echo $toot['replies_count'] ?? 0; ?></span>
            </a>
            <a class="action-btn reblog" href="<?php echo htmlspecialchars($toot['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" title="转发">
              <span class="icon-[ph--repeat-bold]"></span>
              <span><?php echo $toot['reblogs_count'] ?? 0; ?></span>
            </a>
            <a class="action-btn favourite" href="<?php echo htmlspecialchars($toot['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" title="喜欢">
              <span class="icon-[ph--heart-bold]"></span>
              <span><?php echo $toot['favourites_count'] ?? 0; ?></span>
            </a>
            <a class="action-btn share" href="<?php echo htmlspecialchars($toot['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener" title="查看原文">
              <span class="icon-[ph--arrow-square-out-bold]"></span>
            </a>
          </div>
        </footer>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<style>
/* Mastodon 动态页面样式 */
.mastodon-header {
  text-align: center;
  margin-bottom: 2rem;
}

.mastodon-header .page-title {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.mastodon-header .icon-\[ph--mastodon-logo-bold\] {
  font-size: 2rem;
  color: #6364ff;
}

.mastodon-header .page-desc {
  color: var(--c-text-2);
  font-size: 0.95rem;
  margin-bottom: 1rem;
}

.mastodon-link {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.5rem 1rem;
  background: var(--c-bg-2);
  border: 1px solid var(--c-border);
  border-radius: 20px;
  font-size: 0.9rem;
  color: var(--c-text);
  text-decoration: none;
  transition: all 0.2s ease;
}

.mastodon-link:hover {
  background: var(--c-primary);
  border-color: var(--c-primary);
  color: #fff;
}

.moments-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.moment-card {
  background: var(--c-bg-2);
  border: 1px solid var(--c-border);
  border-radius: 12px;
  padding: 1.25rem;
  animation: fadeInUp 0.5s ease forwards;
  animation-delay: var(--delay, 0s);
  opacity: 0;
  transform: translateY(20px);
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.moment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.author-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.author-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.author-name {
  font-weight: 600;
  color: var(--c-text);
}

.reblog-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.2rem 0.5rem;
  background: var(--c-primary);
  color: #fff;
  border-radius: 4px;
  font-size: 0.75rem;
  margin-left: 0.5rem;
}

.moment-time {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.85rem;
  color: var(--c-text-3);
}

.moment-body {
  margin-bottom: 1rem;
}

.moment-text {
  color: var(--c-text);
  line-height: 1.7;
  word-wrap: break-word;
}

.moment-text a {
  color: var(--c-primary);
  text-decoration: none;
}

.moment-text a:hover {
  text-decoration: underline;
}

.moment-text .hashtag {
  color: var(--c-primary);
}

.moment-text .mention {
  color: var(--c-primary);
}

/* 媒体网格 */
.moment-media {
  margin-top: 0.75rem;
  display: grid;
  gap: 0.5rem;
}

.moment-media.single {
  grid-template-columns: 1fr;
  max-width: 400px;
}

.moment-media.double {
  grid-template-columns: repeat(2, 1fr);
}

.moment-media.grid {
  grid-template-columns: repeat(3, 1fr);
}

.media-item {
  margin: 0;
  border-radius: 8px;
  overflow: hidden;
  background: var(--c-bg-3);
}

.media-item img,
.media-item video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.media-item.photo {
  aspect-ratio: 1;
}

/* 底部操作栏 */
.moment-footer {
  padding-top: 0.75rem;
  border-top: 1px solid var(--c-border);
}

.moment-actions {
  display: flex;
  gap: 1rem;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem 0.75rem;
  background: transparent;
  border: none;
  border-radius: 6px;
  font-size: 0.85rem;
  color: var(--c-text-2);
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn:hover {
  background: var(--c-bg-3);
  color: var(--c-text);
}

.action-btn.reply:hover {
  color: #6364ff;
}

.action-btn.reblog:hover {
  color: #00ba7c;
}

.action-btn.favourite:hover {
  color: #ff4757;
}

/* 空状态 */
.moments-empty {
  text-align: center;
  padding: 3rem 1rem;
  color: var(--c-text-2);
}

.moments-empty .icon-\[ph--mastodon-logo-bold\] {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.moments-hint {
  font-size: 0.85rem;
  color: var(--c-text-3);
  margin-top: 0.5rem;
}

/* 响应式 */
@media (max-width: 768px) {
  .moment-media.grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .moment-actions {
    gap: 0.5rem;
  }
  
  .action-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
}
</style>

<?php $this->need('footer.php'); ?>
