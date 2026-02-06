<?php
/**
 * 我的装备
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$equipmentTitle = trim((string) clarity_opt('equipment_title', '我的装备'));
$equipmentDesc = trim((string) clarity_opt('equipment_desc', '我的生产力工具'));
$equipmentCover = trim((string) clarity_opt('equipment_cover', ''));
$equipmentData = trim((string) clarity_opt('equipment_data', ''));

clarity_set('showAside', true);
clarity_set('pageTitle', $equipmentTitle);
clarity_set('isLinksPage', false);

// 图标渲染函数
$renderIcon = function ($icon) {
  $icon = trim((string) $icon);
  if ($icon === '') {
    return '';
  }
  if (preg_match('/icon-\[([a-z0-9]+)--([^\]]+)\]/i', $icon, $match)) {
    $prefix = strtolower($match[1]);
    $name = $match[2];
    $safeName = preg_replace('/[^a-z0-9\-:_]/i', '', $name);
    $iconName = $prefix . ':' . $safeName;
    $iconUrl = 'https://api.iconify.design/' . rawurlencode($prefix) . '/' . rawurlencode($safeName) . '.svg';
    return '<span class="iconify-mask ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '" data-icon="' . htmlspecialchars($iconName, ENT_QUOTES, 'UTF-8') . '" style="--icon-url:url(\'' . htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8') . '\')"></span>';
  }
  return '<span class="' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '"></span>';
};

// 解析装备数据 - 优先从 Enhancement 插件读取
$equipment = [];
$usePlugin = false;

// 检查 Enhancement 插件是否启用且装备表存在
try {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $checkTable = $db->fetchRow($db->query("SELECT 1 FROM {$prefix}equipment LIMIT 1"));
    if ($checkTable !== false) {
        // 从插件数据库读取装备数据
        $pluginEquipment = $db->fetchAll($db->select()->from($prefix . 'equipment')->order($prefix . 'equipment.order', Typecho_Db::SORT_ASC));
        if (!empty($pluginEquipment)) {
            $usePlugin = true;
            foreach ($pluginEquipment as $item) {
                $equipment[] = [
                    'name' => $item['name'],
                    'categroy' => $item['categroy'] ?: '硬件',
                    'desc' => $item['description'] ?? '',
                    'image' => $item['image'],
                    'src' => $item['src'],
                    'info' => json_decode($item['info'] ?: '[]', true),
                    'tag' => json_decode($item['tag'] ?: '[]', true),
                    'date' => $item['date'],
                    'money' => $item['money']
                ];
            }
        }
    }
} catch (Exception $e) {
    // 插件表不存在或出错，使用 functions.php 配置
}

// 如果插件没有数据，使用 functions.php 配置
if (!$usePlugin && $equipmentData !== '') {
    $decoded = json_decode($equipmentData, true);
    if (is_array($decoded)) {
        $equipment = $decoded;
    }
}

// 获取分类列表
$categories = [];
foreach ($equipment as $item) {
    $cat = $item['categroy'] ?? '硬件';
    if (!in_array($cat, $categories)) {
        $categories[] = $cat;
    }
}
if (empty($categories)) {
    $categories = ['硬件', '外设'];
}

// 分类颜色映射
$categoryColors = [
    '硬件' => '#3af',
    '外设' => '#3ba',
    '软件' => '#a3f',
    '其他' => '#f93'
];
?>
<?php $this->need('header.php'); ?>

<div class="equipment-page">
  <!-- 页面头部 Banner -->
  <div class="page-banner">
    <div class="cover-wrapper">
      <?php if ($equipmentCover): ?>
        <img src="<?php echo htmlspecialchars($equipmentCover, ENT_QUOTES, 'UTF-8'); ?>" class="wrapper-image" alt="<?php echo htmlspecialchars($equipmentTitle, ENT_QUOTES, 'UTF-8'); ?>" />
      <?php else: ?>
        <div class="wrapper-image default-cover">
          <?php echo $renderIcon('icon-[ph--laptop-bold]'); ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="header-wrapper">
      <h3 class="title"><?php echo htmlspecialchars($equipmentTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
      <span class="desc"><?php echo htmlspecialchars($equipmentDesc, ENT_QUOTES, 'UTF-8'); ?></span>
    </div>
  </div>

  <div id="icat-equipment">
    <div class="equipment-category">
      <!-- 顶部导航栏 -->
      <div class="categories-tabs">
        <div class="tabs-container">
          <?php foreach ($categories as $category): ?>
            <?php
            $color = $categoryColors[$category] ?? '#3af';
            $icon = $category === '硬件' ? 'ph--laptop-bold' : ($category === '外设' ? 'ph--package-bold' : 'ph--device-bold');
            $count = count(array_filter($equipment, function($item) use ($category) {
                return ($item['categroy'] ?? '硬件') === $category;
            }));
            ?>
            <div
              class="category-tab"
              data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"
              style="--tab-color: <?php echo htmlspecialchars($color, ENT_QUOTES, 'UTF-8'); ?>"
              onclick="switchCategory('<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>')"
            >
              <?php echo $renderIcon('icon-[' . $icon . ']'); ?>
              <span><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></span>
              <span class="count"><?php echo $count; ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 设备展示区 -->
      <div class="equipment-list" id="equipment-list">
        <?php foreach ($equipment as $index => $item): ?>
          <?php
          $name = $item['name'] ?? '未知设备';
          $image = $item['image'] ?? '';
          $src = $item['src'] ?? '#';
          $category = $item['categroy'] ?? '硬件';
          $desc = $item['desc'] ?? '';
          $info = $item['info'] ?? [];
          $tags = $item['tag'] ?? [];
          $date = $item['date'] ?? '';
          $money = $item['money'] ?? 0;
          $color = $categoryColors[$category] ?? '#3af';
          ?>
          <div class="equipment-card" data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="equipment-image">
              <?php if ($image): ?>
                <img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" />
              <?php else: ?>
                <div class="no-image">
                  <?php echo $renderIcon('icon-[ph--image-bold]'); ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="equipment-content">
              <div class="equipment-header">
                <h3 class="card-name"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="card-category" style="--category-color: <?php echo htmlspecialchars($color, ENT_QUOTES, 'UTF-8'); ?>">
                  <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                </div>
              </div>
              <div class="equipment-opinion">
                <?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?>
              </div>
              <?php if (!empty($info)): ?>
                <div class="card-specs">
                  <?php foreach ($info as $key => $value): ?>
                    <div class="spec-item">
                      <div class="spec-label"><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?></div>
                      <div class="spec-value"><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <?php if (!empty($tags)): ?>
                <div class="card-tags">
                  <?php foreach ($tags as $tag): ?>
                    <span class="tag"><?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <div class="card-footer">
                <div class="purchase-info">
                  <?php echo $renderIcon('icon-[ph--calendar-bold]'); ?>
                  <span><?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="price-info">
                  ￥<?php echo number_format($money); ?>
                </div>
              </div>
              <div class="equipment-actions">
                <a class="equipment-link" href="<?php echo htmlspecialchars($src, ENT_QUOTES, 'UTF-8'); ?>" title="跳转到<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>的产品详情" target="_blank" rel="noopener noreferrer">
                  详情
                </a>
                <button class="comment-btn" type="button" aria-label="快速评论">
                  <?php echo $renderIcon('icon-[ph--chats-bold]'); ?>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if (empty($equipment)): ?>
        <div class="equipment-empty">
          <?php echo $renderIcon('icon-[ph--laptop-bold]'); ?>
          <p>暂无装备数据</p>
          <p class="hint">请在主题设置中添加装备数据</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($this->allow('comment')): ?>
    <h3 class="comment-title">
      <?php echo $renderIcon('icon-[ph--chat-circle-text-bold]'); ?>
      评论区
    </h3>
    <section class="z-comment" id="comments">
      <?php $this->need('comments.php'); ?>
    </section>
  <?php endif; ?>
</div>

<style>
/* 页面头部 Banner */
.equipment-page {
  animation: float-in .2s backwards;
}

.page-banner {
  margin-left: 1rem;
  margin-right: 1rem;
  margin-top: 1rem;
}

.page-banner .cover-wrapper {
  border-radius: 1rem;
  height: 300px;
  overflow: hidden;
  overflow: clip;
  position: relative;
}

.page-banner .cover-wrapper .wrapper-image {
  height: 100%;
  object-fit: cover;
  width: 100%;
  display: block;
}

.page-banner .cover-wrapper .wrapper-image.default-cover {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--c-bg-soft) 0%, var(--c-bg-card) 100%);
}

.page-banner .cover-wrapper .wrapper-image.default-cover span {
  font-size: 5rem;
  color: var(--c-primary);
  opacity: 0.5;
}

.page-banner .header-wrapper {
  margin-top: 12px;
  padding: 0.8rem 1.3rem;
  border-radius: 1rem;
  background-color: var(--c-bg-2);
}

.page-banner .header-wrapper .title {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0 0 0.5rem;
  color: var(--c-text-1);
}

.page-banner .header-wrapper .desc {
  font-size: 0.9rem;
  color: var(--c-text-2);
  display: block;
}

@media (max-width: 768px) {
  .page-banner .cover-wrapper {
    height: auto;
    min-height: 200px;
  }

  .page-banner .header-wrapper .title {
    font-size: 1.25rem;
  }

  .page-banner .header-wrapper .desc {
    font-size: 0.85rem;
  }
}

/* 装备页面样式 */
#icat-equipment {
  padding-bottom: 12px;
  --category-color-one: #3af;
  --category-color-two: #3ba;
}

#icat-equipment .equipment-category {
  margin: 1rem;
  padding-top: 1rem;
}

/* 分类标签 */
#icat-equipment .categories-tabs {
  display: flex;
  justify-content: center;
  margin-bottom: 2rem;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

#icat-equipment .tabs-container {
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;
  justify-content: center;
  padding: .5rem;
}

#icat-equipment .category-tab {
  align-items: center;
  background: transparent;
  border: 2px solid var(--c-border);
  border-radius: .6rem;
  color: var(--c-text-2);
  cursor: pointer;
  display: flex;
  font-size: .95rem;
  gap: .5rem;
  padding: .6rem 1.2rem;
  transition: all .3s ease;
  white-space: nowrap;
}

#icat-equipment .category-tab.active {
  background: color-mix(in srgb, var(--tab-color) 10%, transparent);
  border-color: var(--tab-color);
  color: var(--tab-color);
  font-weight: 600;
}

#icat-equipment .category-tab:hover {
  border-color: var(--tab-color);
  color: var(--tab-color);
}

#icat-equipment .category-tab .count {
  background: var(--c-bg-soft);
  border-radius: .3rem;
  font-size: .75rem;
  padding: .1rem .4rem;
}

#icat-equipment .category-tab.active .count {
  background: color-mix(in srgb, var(--tab-color) 20%, transparent);
}

/* 装备列表 */
#icat-equipment .equipment-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  padding: 10px 0 0;
}

/* 装备卡片 */
#icat-equipment .equipment-card {
  border: 1px solid var(--c-border);
  background: var(--c-bg-card);
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

#icat-equipment .equipment-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

#icat-equipment .equipment-card.hidden {
  display: none;
}

/* 装备图片 */
#icat-equipment .equipment-image {
  align-items: center;
  display: flex;
  height: 240px;
  justify-content: center;
  position: relative;
  width: 100%;
  background: var(--c-bg-soft);
  overflow: hidden;
}

#icat-equipment .equipment-image img {
  height: 100%;
  object-fit: contain;
  width: 100%;
  padding: 0.8rem;
  transition: transform 0.3s;
}

#icat-equipment .equipment-image:hover img {
  transform: scale(1.05);
}

#icat-equipment .equipment-image .no-image {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

#icat-equipment .equipment-image .no-image span {
  font-size: 3rem;
  color: var(--c-text-3);
}

/* 装备内容 */
#icat-equipment .equipment-content {
  padding: 16px;
  flex: 1;
  flex-direction: column;
  gap: .8rem;
  min-width: 0;
  padding: 1rem;
  display: flex;
}

/* 装备头部 */
#icat-equipment .equipment-header {
  align-items: flex-start;
  gap: .8rem;
  display: flex;
  justify-content: space-between;
}

#icat-equipment .card-name {
  color: var(--c-text-1);
  font-size: 1.125rem;
  font-weight: 700;
  line-height: 1.2;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
}

#icat-equipment .card-category {
  background: color-mix(in srgb, var(--category-color) 10%, transparent);
  border-radius: .4rem;
  color: var(--category-color);
  flex-shrink: 0;
  font-size: .75rem;
  font-weight: 600;
  padding: .3rem .8rem;
  white-space: nowrap;
}

/* 装备描述 */
#icat-equipment .equipment-opinion {
  color: var(--c-text-2);
  display: -webkit-box;
  font-size: .9rem;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  line-height: 1.4;
  margin: 0;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-break: break-word;
}

/* 规格参数 */
#icat-equipment .card-specs {
  background: transparent;
  border-radius: 0;
  display: grid;
  font-size: .8rem;
  gap: .4rem;
  grid-template-columns: repeat(2, 1fr);
  padding: 0;
}

#icat-equipment .spec-item {
  display: flex;
  flex-direction: column;
  gap: .1rem;
}

#icat-equipment .spec-label {
  color: var(--c-text-2);
  font-size: .7rem;
  font-weight: 500;
}

#icat-equipment .spec-value {
  color: var(--c-text);
  font-size: .8rem;
  word-break: break-word;
}

/* 标签 */
#icat-equipment .card-tags {
  display: flex;
  flex-wrap: wrap;
  gap: .3rem;
}

#icat-equipment .card-tags .tag {
  background: color-mix(in srgb, var(--c-primary) 10%, transparent);
  border-radius: .3rem;
  color: var(--c-primary);
  display: inline-block;
  font-size: .7rem;
  padding: .15rem .5rem;
  white-space: nowrap;
}

/* 卡片底部 */
#icat-equipment .card-footer {
  border-top: 1px solid var(--c-border);
  color: var(--c-text-2);
  display: flex;
  flex-wrap: wrap;
  font-size: .75rem;
  gap: .8rem;
  padding-top: .6rem;
  align-items: center;
}

#icat-equipment .card-footer > div {
  display: flex;
  align-items: center;
  gap: .3rem;
}

#icat-equipment .price-info {
  color: var(--c-primary);
  font-weight: 600;
  margin-left: auto;
}

/* 操作按钮 */
#icat-equipment .equipment-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: .5rem;
}

#icat-equipment .equipment-link {
  font-size: 0.75rem;
  background: var(--c-bg-soft);
  color: var(--c-text);
  padding: 6px 12px;
  border-radius: 6px;
  letter-spacing: 0.5px;
  text-decoration: none;
  transition: all 0.3s ease;
  flex: 1;
  text-align: center;
}

#icat-equipment .equipment-link:hover {
  color: #fff;
  background: var(--c-primary);
  box-shadow: 0 8px 16px -4px var(--ld-shadow);
}

#icat-equipment .comment-btn {
  background: var(--c-bg-soft);
  color: var(--c-text);
  border: none;
  border-radius: 6px;
  padding: 6px 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

#icat-equipment .comment-btn:hover {
  color: #fff;
  background: var(--c-primary);
  box-shadow: 0 8px 16px -4px var(--ld-shadow);
}

/* 空状态 */
.equipment-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  color: var(--c-text-2);
  text-align: center;
}

.equipment-empty span {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.equipment-empty p {
  margin: 0.5rem 0;
  font-size: 1.1rem;
}

.equipment-empty .hint {
  font-size: 0.85rem;
  opacity: 0.7;
}

/* 评论区标题 */
.comment-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 2rem 1rem 1rem;
  font-size: 1.2rem;
  color: var(--c-text-1);
}

/* 动画 */
@keyframes float-in {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* 响应式设计 */
@media screen and (max-width: 1024px) {
  #icat-equipment .equipment-list {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}

@media screen and (max-width: 768px) {
  #icat-equipment .equipment-category {
    margin: 0.5rem;
  }

  #icat-equipment .equipment-list {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  #icat-equipment .equipment-image {
    height: 200px;
  }
}

@media screen and (max-width: 480px) {
  #icat-equipment .equipment-category {
    margin: 0.25rem;
  }

  #icat-equipment .equipment-image {
    height: 160px;
  }

  #icat-equipment .card-name {
    font-size: 1rem;
  }
}

/* 无障碍支持 */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>

<script>
(function() {
  // 切换分类
  window.switchCategory = function(category) {
    // 更新标签状态
    document.querySelectorAll('.category-tab').forEach(tab => {
      if (tab.dataset.category === category) {
        tab.classList.add('active');
      } else {
        tab.classList.remove('active');
      }
    });

    // 显示/隐藏装备卡片
    document.querySelectorAll('.equipment-card').forEach(card => {
      if (card.dataset.category === category) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  };

  // 默认显示第一个分类
  const categories = document.querySelectorAll('.category-tab');
  if (categories.length > 0) {
    const firstCategory = categories[0].dataset.category;
    switchCategory(firstCategory);
  }

  // 快速评论功能 - 仅跳转到评论区
  document.querySelectorAll('.comment-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var respond = document.querySelector('.comment-respond');
      if (respond) {
        respond.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      var textarea = document.querySelector('textarea.comment-textarea') || document.querySelector('textarea[name="text"]');
      if (textarea) {
        textarea.focus();
        if (typeof TypechoComment !== 'undefined' && TypechoComment.cancelReply) {
          TypechoComment.cancelReply();
        }
      }
    });
  });
})();
</script>

<?php $this->need('footer.php'); ?>
