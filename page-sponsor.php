<?php
/**
 * 赞助
 *
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 获取主题设置中的打赏二维码
$rewardAlipay = clarity_opt('reward_alipay', '');
$rewardWechat = clarity_opt('reward_wechat', '');

// 获取赞助者数据
$sponsors = [];
$sponsorsUrl = 'https://sponsors.314926.xyz/sponsors/sponsors.json';

// 使用 cURL 获取远程 JSON 数据
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sponsorsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $response) {
    // 清理可能的 BOM 和多余空白
    $response = trim($response);
    if (substr($response, 0, 3) === "\xEF\xBB\xBF") {
        $response = substr($response, 3);
    }
    
    $data = json_decode($response, true);
    
    // 检查 JSON 解析错误
    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
        // 检查是否是关联数组（对象格式）
        $keys = array_keys($data);
        $isAssoc = !empty($keys) && !is_int($keys[0]);
        
        if ($isAssoc && isset($data['name'])) {
            // 单个赞助者对象，包装成数组
            $sponsors = [$data];
        } else {
            // 已经是数组格式，过滤掉非数组项并重新索引
            $sponsors = array_values(array_filter($data, 'is_array'));
        }
    }
}

$this->need('header.php');
?>

<main class="main-content">
  <article class="post-content sponsor-page">
    <header class="post-header center-title">
      <h1 class="post-title">
        <span class="icon-[ph--heart-fill]" style="color: var(--c-primary);"></span>
        赞助支持
      </h1>
    </header>

    <div class="sponsor-content">
      <!-- 介绍文字 -->
      <div class="sponsor-intro card-base">
        <p class="sponsor-desc">
          如果您觉得我的内容对您有帮助，欢迎通过以下方式支持我的创作。您的每一份支持都是我持续创作的动力！
        </p>
        <p class="sponsor-subdesc">
          所有赞助将用于网站维护、服务器费用以及内容创作。
        </p>
      </div>

      <!-- 打赏二维码 -->
      <?php if ($rewardAlipay || $rewardWechat): ?>
      <div class="donate-section">
        <div class="donate-grid">
          <?php if ($rewardAlipay): ?>
          <div class="donate-card">
            <div class="donate-header">
              <div class="donate-icon alipay">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 48 48"><path fill="#ffffff" d="M24 0c13.255 0 24 10.745 24 24S37.255 48 24 48S0 37.255 0 24S10.745 0 24 0m0 4.364C13.155 4.364 4.364 13.155 4.364 24S13.155 43.636 24 43.636c6.738 0 12.683-3.393 16.22-8.565a64 64 0 0 1-6.683-2.402q-1.855-.78-5.541-2.459C25.056 33.173 20.769 36 16.174 36c-3.188-.016-8.538-1.627-8.538-6.654s4.956-6.224 8.172-6.224q2.919 0 10.33 3.222l.077.03q1.989-2.434 2.73-5.653l.1-.464h-12.87v-2.32h5.857v-2.9h-8.201v-1.74h8.201l.001-3.479h4.687v3.48h9.374v1.74H26.72v2.899h7.486q-.165 1.086-.371 1.973l-.084.347q-.517 1.868-2.325 5.442c-.32.632-.782 1.364-1.366 2.139q2.766 1.024 5.6 1.99q3.67 1.249 6.345 2.02A19.6 19.6 0 0 0 43.636 24c0-10.845-8.791-19.636-19.636-19.636m-13.685 24.18c0 3.198 6.69 4.472 12.11 1.106q.915-.57 1.71-1.213l-.018-.008l-.427-.262q-5.074-3.087-7.882-3.348c-1.719-.159-5.493.527-5.493 3.725m18.73-8.287h2.209z"/></svg>
              </div>
              <div class="donate-info">
                <h3>支付宝</h3>
                <p>扫码支付</p>
              </div>
            </div>
            <div class="donate-qr">
              <img src="<?php echo htmlspecialchars($rewardAlipay, ENT_QUOTES, 'UTF-8'); ?>" alt="支付宝二维码" loading="lazy" class="sponsor-qr-img" data-type="alipay" />
            </div>
          </div>
          <?php endif; ?>

          <?php if ($rewardWechat): ?>
          <div class="donate-card">
            <div class="donate-header">
              <div class="donate-icon wechat">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#ffffff" stroke-linejoin="round"><path stroke-linecap="round" stroke-width="2" d="M7 7h.009m5.982 0H13m4.991 7.5H18m-4 0h.009"/><path stroke-width="1.5" d="M10 16c0 2.761 2.686 5 6 5c.907 0 1.767-.168 2.538-.468c.189-.073.393-.1.592-.063L22 21l-.652-2.03a1.13 1.13 0 0 1 .11-.89A4.3 4.3 0 0 0 22 16c0-2.761-2.686-5-6-5s-6 2.239-6 5Z"/><path stroke-width="1.5" d="M17.873 11.249Q18 10.639 18 10c0-3.866-3.582-7-8-7s-8 3.134-8 7c0 1.112.297 2.164.824 3.098c.147.26.196.567.108.853L2 17l3.914-.76c.208-.041.422-.013.617.07a9 9 0 0 0 3.589.69"/></g></svg>
              </div>
              <div class="donate-info">
                <h3>微信支付</h3>
                <p>扫码支付</p>
              </div>
            </div>
            <div class="donate-qr">
              <img src="<?php echo htmlspecialchars($rewardWechat, ENT_QUOTES, 'UTF-8'); ?>" alt="微信支付二维码" loading="lazy" class="sponsor-qr-img" data-type="wechat" />
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- 其他支持方式 -->
      <div class="support-section card-base">
        <h2 class="section-title">
          <span class="icon-[ph--star-bold]"></span>
          其他支持方式
        </h2>
        <div class="support-grid">
          <div class="support-card">
            <div class="support-header">
              <span class="icon-[ph--share-network-bold]"></span>
              <span class="support-name">分享推荐</span>
            </div>
            <p class="support-desc">将我的博客分享给更多朋友</p>
          </div>

          <div class="support-card">
            <div class="support-header">
              <span class="icon-[ph--chat-circle-dots-bold]"></span>
              <span class="support-name">留言互动</span>
            </div>
            <p class="support-desc">在文章下方留下您的想法</p>
          </div>

          <div class="support-card">
            <div class="support-header">
              <span class="icon-[ph--rss-bold]"></span>
              <span class="support-name">关注订阅</span>
            </div>
            <p class="support-desc">订阅RSS或关注社交媒体</p>
          </div>
        </div>
      </div>

      <!-- 赞助者名单 -->
      <?php if (!empty($sponsors)): ?>
      <div class="sponsors-section card-base">
        <h2 class="section-title">
          <span class="icon-[ph--users-bold]"></span>
          已赞助的小伙伴
        </h2>
        <p class="sponsors-subtitle">
          如果您已赞助，并且想加入赞助名单，<a href="https://github.com/mcyzsx/mcyzsx/edit/main/sponsors/sponsors.json" target="_blank" rel="noopener noreferrer" class="link-primary">请点击这里提交</a>
        </p>
        <div class="sponsors-grid">
          <?php 
          $sponsorCount = 0;
          foreach ($sponsors as $index => $sponsor): 
            // 跳过非数组项
            if (!is_array($sponsor)) continue;
            // 最多显示20个
            if ($sponsorCount >= 20) break;
            $sponsorCount++;
          ?>
          <div class="sponsor-card">
            <div class="sponsor-inner">
              <div class="sponsor-avatar">
                <?php if (!empty($sponsor['avatar'])): ?>
                  <img src="<?php echo htmlspecialchars($sponsor['avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($sponsor['name'] ?? '匿名', ENT_QUOTES, 'UTF-8'); ?>的头像" loading="lazy" />
                <?php else: ?>
                  <span class="icon-[ph--user-circle-bold]"></span>
                <?php endif; ?>
              </div>
              <div class="sponsor-info">
                <h4 class="sponsor-name"><?php echo htmlspecialchars($sponsor['name'] ?? '匿名用户', ENT_QUOTES, 'UTF-8'); ?></h4>
                <p class="sponsor-date"><?php echo htmlspecialchars($sponsor['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
              </div>
              <div class="sponsor-amount"><?php echo htmlspecialchars($sponsor['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php else: ?>
      <!-- 调试信息：数据为空时显示 -->
      <div class="sponsors-section card-base">
        <h2 class="section-title">
          <span class="icon-[ph--users-bold]"></span>
          已赞助的小伙伴
        </h2>
        <p style="color: var(--c-text-2); text-align: center; padding: 2rem;">
          暂无赞助数据或数据加载失败
        </p>
      </div>
      <?php endif; ?>
    </div>
  </article>
</main>

<script>
(function() {
  // 为二维码图片添加点击事件，使用 fancybox 打开
  document.querySelectorAll('.sponsor-qr-img').forEach(function(img) {
    img.style.cursor = 'zoom-in';
    img.addEventListener('click', function() {
      const type = this.getAttribute('data-type');
      const typeName = type === 'alipay' ? '支付宝' : '微信支付';
      
      const openFancybox = function() {
        if (window.Fancybox) {
          window.Fancybox.show([{
            src: img.src,
            caption: typeName + '二维码'
          }], {
            Toolbar: {
              display: {
                left: ['infobar'],
                middle: ['zoomIn', 'zoomOut', 'toggle1to1', 'rotateCCW', 'rotateCW', 'flipX', 'flipY'],
                right: ['slideshow', 'thumbs', 'close']
              }
            }
          });
        }
      };
      
      // 如果 fancybox 已加载，直接打开
      if (window.Fancybox) {
        openFancybox();
        return;
      }
      
      // 尝试加载 fancybox
      if (window.__clarityFancyboxLoading) {
        window.__clarityFancyboxLoading.then(function() {
          openFancybox();
        });
      } else {
        // 延迟后重试
        setTimeout(function() {
          if (window.Fancybox) {
            openFancybox();
          }
        }, 1000);
      }
    });
  });
})();
</script>

<?php $this->need('footer.php'); ?>
