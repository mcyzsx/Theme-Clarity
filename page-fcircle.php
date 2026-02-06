<?php
/**
 * 友链朋友圈
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$fcircleTitle = trim((string) clarity_opt('fcircle_title', '友链朋友圈'));
$fcircleDesc = trim((string) clarity_opt('fcircle_desc', '汇聚友链博客的最新动态'));
$fcircleApiUrl = trim((string) clarity_opt('fcircle_api_url', 'https://moments.myxz.top/'));
$fcirclePageSize = (int) clarity_opt('fcircle_page_size', '20');
$fcircleErrorImg = trim((string) clarity_opt('fcircle_error_img', 'https://fastly.jsdelivr.net/gh/willow-god/Friend-Circle-Lite@latest/static/favicon.ico'));
$fcircleCover = trim((string) clarity_opt('fcircle_cover', ''));

clarity_set('showAside', true);
clarity_set('pageTitle', $fcircleTitle);
clarity_set('isLinksPage', false);
?>
<?php $this->need('header.php'); ?>

<div class="fcircle-page">
  <!-- 页面头部 Banner -->
  <div class="page-banner">
    <div class="cover-wrapper">
      <?php if ($fcircleCover): ?>
        <img src="<?php echo htmlspecialchars($fcircleCover, ENT_QUOTES, 'UTF-8'); ?>" class="wrapper-image" alt="<?php echo htmlspecialchars($fcircleTitle, ENT_QUOTES, 'UTF-8'); ?>" />
      <?php else: ?>
        <div class="wrapper-image default-cover">
          <span class="icon-[ph--users-three-bold]"></span>
        </div>
      <?php endif; ?>
    </div>
    <div class="header-wrapper">
      <h3 class="title"><?php echo htmlspecialchars($fcircleTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
      <span class="desc"><?php echo htmlspecialchars($fcircleDesc, ENT_QUOTES, 'UTF-8'); ?></span>
    </div>
  </div>

  <!-- 统计信息 -->
  <div class="fcircle-stats">
    <div class="stat-item">
      <span class="stat-value" id="stat-friends">-</span>
      <span class="stat-label">友链数</span>
    </div>
    <div class="stat-item">
      <span class="stat-value" id="stat-active">-</span>
      <span class="stat-label">活跃数</span>
    </div>
    <div class="stat-item">
      <span class="stat-value" id="stat-articles">-</span>
      <span class="stat-label">文章数</span>
    </div>
    <div class="stat-item">
      <span class="stat-value" id="stat-updated">-</span>
      <span class="stat-label">更新于</span>
    </div>
  </div>

  <div class="article-list">
    <!-- 随机文章区域 -->
    <div class="random-article" id="random-article-section" style="display: none;">
      <div class="random-container-title">随机钓鱼</div>
      <a href="#" id="random-article-link" class="article-item" target="_blank" rel="noopener noreferrer">
        <div class="article-container gradient-card">
          <div class="article-author" id="random-article-author"></div>
          <div class="article-title" id="random-article-title"></div>
          <div class="article-date" id="random-article-date"></div>
        </div>
      </a>
      <button class="refresh-btn gradient-card" id="refresh-random-btn" title="换一篇">
        <span class="icon-[ph--shuffle-bold]"></span>
      </button>
    </div>

    <!-- 文章列表区域 -->
    <div class="articles-list" id="articles-list">
      <!-- 文章将通过 JS 动态加载 -->
    </div>

    <div class="load-more-container">
      <button class="load-more gradient-card" id="load-more-btn">
        再来亿点
      </button>
    </div>
  </div>

  <!-- 作者模态框 -->
  <div id="author-modal" class="modal">
    <div class="modal-content">
      <div class="modal__header">
        <img id="modal-author-avatar" src="" alt="" />
        <a id="modal-author-name-link" href="#" target="_blank" rel="noopener noreferrer"></a>
      </div>
      <div id="modal-articles-container">
        <!-- 作者文章列表 -->
      </div>
      <img id="modal-bg" src="" alt="" />
    </div>
  </div>
</div>

<style>
/* 页面头部 Banner - 复刻 header.vue 样式 */
.fcircle-page {
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

/* 统计信息 */
.fcircle-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin: 1rem;
  padding: 1rem;
  background: var(--c-bg-2);
  border-radius: 1rem;
}

.fcircle-stats .stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.fcircle-stats .stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--c-primary);
}

.fcircle-stats .stat-label {
  font-size: 0.8rem;
  color: var(--c-text-2);
}

@media (max-width: 768px) {
  .fcircle-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* 文章列表 */
.article-list {
  margin: 1rem;
}

.random-article {
  align-items: center;
  display: flex;
  flex-direction: row;
  gap: 10px;
  justify-content: space-between;
  margin: 1rem 0;
}

.random-article .random-container-title {
  font-size: 1.2rem;
  white-space: nowrap;
  color: var(--c-text-1);
  font-weight: 500;
}

.random-article .article-item {
  flex: 1;
  min-width: 0;
  text-decoration: none;
}

.random-article .refresh-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--c-text-2);
  cursor: pointer;
  flex-shrink: 0;
  height: 2.5rem;
  width: 2.5rem;
  border: none;
  background: var(--c-bg-card);
  border-radius: 8px;
  box-shadow: 0 0 0 1px var(--c-bg-soft);
  transition: all 0.2s ease;
}

.random-article .refresh-btn:hover {
  color: var(--c-text);
  background: var(--c-bg-soft);
}

.articles-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

/* 文章项 */
.article-item {
  align-items: center;
  display: flex;
  gap: 10px;
  width: 100%;
}

.article-item.new-item {
  animation: float-in 0.2s var(--delay) backwards;
}

.article-image {
  border-radius: 50%;
  box-shadow: 0 0 0 1px var(--c-bg-soft);
  display: flex;
  flex-shrink: 0;
  height: 2rem;
  overflow: hidden;
  width: 2rem;
  cursor: pointer;
}

.article-image img {
  height: 100%;
  object-fit: cover;
  opacity: 0.8;
  transition: all 0.2s;
  width: 100%;
}

.article-image:hover img {
  opacity: 1;
  transform: scale(1.05);
}

.article-container {
  border-radius: 8px;
  box-shadow: 0 0 0 1px var(--c-bg-soft);
  transition: all 0.2s ease;
  align-items: center;
  display: flex;
  gap: 5px;
  height: 2.5rem;
  overflow: hidden;
  padding: 10px;
  width: 100%;
  cursor: pointer;
  background: var(--c-bg-card);
}

.article-container:hover {
  background: var(--c-bg-soft);
}

.article-container:hover .article-title {
  color: var(--c-text);
}

.article-container .article-author {
  color: var(--c-text-3);
  font-size: 0.85rem;
  flex-shrink: 0;
}

.article-container .article-title {
  color: var(--c-text-2);
  flex: 1;
  font-size: 0.9375rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  transition: color 0.2s;
}

.article-container .article-date {
  color: var(--c-text-3);
  font-family: var(--font-monospace, 'JetBrains Mono', monospace);
  font-size: 0.75rem;
  flex-shrink: 0;
}

/* 加载更多 */
.load-more-container {
  text-align: center;
  margin: 1rem 0;
}

.load-more {
  background-color: var(--c-bg-card);
  border: none;
  border-radius: 8px;
  box-shadow: 0.1em 0.2em 0.5rem var(--ld-shadow);
  display: inline-block;
  font-size: 0.875rem;
  height: 42px;
  padding: 0.75rem;
  width: 200px;
  cursor: pointer;
  color: var(--c-text-2);
  transition: all 0.2s ease;
}

.load-more:hover:not(:disabled) {
  color: var(--c-text);
  background: var(--c-bg-soft);
}

.load-more:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.load-more.loading {
  pointer-events: none;
}

.load-more.loading::after {
  content: '';
  display: inline-block;
  width: 1em;
  height: 1em;
  border: 2px solid var(--c-text-3);
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-left: 0.5em;
  vertical-align: middle;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* 模态框 */
.modal {
  align-items: center;
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  display: none;
  justify-content: center;
  inset: 0;
  position: fixed;
  z-index: 100;
}

.modal.modal-open {
  display: flex;
}

.modal .modal-content {
  background-color: var(--c-bg-a50, rgba(var(--c-bg-rgb, 255, 255, 255), 0.5));
  border-radius: 12px;
  box-shadow: 0 0 0 1px var(--c-bg-soft);
  max-height: 80vh;
  max-width: 500px;
  overflow-y: auto;
  padding: 1.25rem;
  position: relative;
  width: 90%;
}

.modal .modal__header {
  align-items: center;
  border-bottom: 1px solid var(--c-bg-soft);
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
  padding-bottom: 15px;
}

.modal .modal__header img {
  border-radius: 50%;
  height: 50px;
  object-fit: cover;
  width: 50px;
}

.modal .modal__header a {
  border-radius: 8px;
  color: var(--c-text-2);
  padding: 8px;
  text-decoration: none;
  transition: all 0.3s;
  font-weight: 500;
}

.modal .modal__header a:hover {
  background: var(--c-bg-soft);
  color: var(--c-text);
}

#modal-articles-container .modal-article {
  animation: float-in 0.3s var(--delay) backwards;
  color: var(--c-text-2);
  padding: 0 0 1rem 1.25rem;
  position: relative;
}

#modal-articles-container .modal-article:not(:last-child) {
  border-bottom: 1px solid var(--c-bg-soft);
  padding-bottom: 1rem;
  margin-bottom: 1rem;
}

#modal-articles-container .modal-article .modal-article-title {
  color: var(--c-text-2);
  display: block;
  line-height: 1.4;
  text-decoration: none;
  transition: color 0.3s;
}

#modal-articles-container .modal-article .modal-article-title:hover {
  color: var(--c-text);
}

#modal-articles-container .modal-article .modal-article-date {
  color: var(--c-text-3);
  font-family: var(--font-monospace, 'JetBrains Mono', monospace);
  font-size: 0.875rem;
  margin-top: 0.3rem;
}

#modal-bg {
  border-radius: 50%;
  bottom: 1.25rem;
  filter: blur(5px);
  height: 128px;
  opacity: 0.6;
  overflow: hidden;
  pointer-events: none;
  position: absolute;
  right: 1.25rem;
  width: 128px;
  z-index: 1;
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

/* 响应式 */
@media (max-width: 768px) {
  .page-banner .cover-wrapper {
    height: 200px;
  }

  .page-banner .header-wrapper h1 {
    font-size: 1.5rem;
  }

  .page-banner .header-wrapper p {
    font-size: 0.9rem;
  }

  .random-article .random-container-title {
    display: none;
  }

  .article-item .article-container {
    flex-wrap: wrap;
    height: auto;
    min-height: 2.5rem;
  }

  .article-item .article-container .article-author {
    flex-grow: 1;
  }

  .article-item .article-container .article-title {
    flex-basis: 100%;
    order: 3;
    white-space: normal;
    line-height: 1.4;
  }

  .fcircle-page {
    margin: 0.5rem;
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

/* 打印样式 */
@media print {
  .modal,
  .random-article,
  .load-more-container,
  .fcircle-stats {
    display: none !important;
  }
}
</style>

<script>
(function() {
  // 配置
  const config = {
    apiUrl: '<?php echo htmlspecialchars($fcircleApiUrl, ENT_QUOTES, 'UTF-8'); ?>',
    pageSize: <?php echo $fcirclePageSize; ?>,
    errorImg: '<?php echo htmlspecialchars($fcircleErrorImg, ENT_QUOTES, 'UTF-8'); ?>',
    cacheKey: 'friend-circle-lite-cache',
    cacheTimeKey: 'friend-circle-lite-cache-time'
  };

  // 状态
  let allArticles = [];
  let displayedArticles = [];
  let start = 0;
  let hasMoreArticles = true;
  let randomArticle = null;

  // DOM 元素
  const articlesList = document.getElementById('articles-list');
  const loadMoreBtn = document.getElementById('load-more-btn');
  const randomSection = document.getElementById('random-article-section');
  const randomLink = document.getElementById('random-article-link');
  const randomAuthor = document.getElementById('random-article-author');
  const randomTitle = document.getElementById('random-article-title');
  const randomDate = document.getElementById('random-article-date');
  const refreshBtn = document.getElementById('refresh-random-btn');
  const modal = document.getElementById('author-modal');

  // 统计数据元素
  const statFriends = document.getElementById('stat-friends');
  const statActive = document.getElementById('stat-active');
  const statArticles = document.getElementById('stat-articles');
  const statUpdated = document.getElementById('stat-updated');

  // 初始化
  function init() {
    loadMoreArticles();
    bindEvents();
  }

  // 绑定事件
  function bindEvents() {
    loadMoreBtn.addEventListener('click', loadMoreArticles);
    refreshBtn.addEventListener('click', displayRandomArticle);

    // 模态框关闭
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        hideModal();
      }
    });

    // ESC 关闭模态框
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && modal.classList.contains('modal-open')) {
        hideModal();
      }
    });
  }

  // 加载更多文章
  async function loadMoreArticles() {
    if (loadMoreBtn.classList.contains('loading')) return;

    loadMoreBtn.classList.add('loading');
    loadMoreBtn.textContent = '加载中...';

    const now = new Date().getTime();

    try {
      // 检查缓存
      const cacheTime = localStorage.getItem(config.cacheTimeKey);
      if (cacheTime && (now - parseInt(cacheTime) < 10 * 60 * 1000)) {
        const cachedData = JSON.parse(localStorage.getItem(config.cacheKey));
        if (cachedData) {
          processArticles(cachedData);
          loadMoreBtn.classList.remove('loading');
          loadMoreBtn.textContent = '再来亿点';
          return;
        }
      }

      // 从 API 获取数据
      const response = await fetch(config.apiUrl + 'all.json');
      if (!response.ok) throw new Error('加载失败');
      const data = await response.json();

      // 更新缓存
      localStorage.setItem(config.cacheKey, JSON.stringify(data));
      localStorage.setItem(config.cacheTimeKey, now.toString());

      processArticles(data);
    } catch (error) {
      console.error('加载文章失败:', error);
      loadMoreBtn.textContent = '加载失败，点击重试';
    } finally {
      loadMoreBtn.classList.remove('loading');
    }
  }

  // 处理文章数据
  function processArticles(data) {
    // 更新统计数据
    if (data.statistical_data) {
      statFriends.textContent = data.statistical_data.friends_num || 0;
      statActive.textContent = data.statistical_data.active_num || 0;
      statArticles.textContent = data.statistical_data.article_num || 0;
      statUpdated.textContent = formatDate(data.statistical_data.last_updated_time) || '-';
    }

    // 合并新旧文章
    const newArticles = data.article_data || [];
    allArticles = [...allArticles, ...newArticles];

    // 更新显示的列表
    const newDisplayed = allArticles.slice(start, start + config.pageSize);
    displayedArticles = [...displayedArticles, ...newDisplayed];

    // 渲染新文章
    renderArticles(newDisplayed, start);

    // 更新起始位置
    start += config.pageSize;

    // 检查是否有更多文章
    hasMoreArticles = start < allArticles.length;
    if (!hasMoreArticles) {
      loadMoreBtn.style.display = 'none';
    }

    // 显示随机文章
    if (!randomArticle && allArticles.length > 0) {
      displayRandomArticle();
      randomSection.style.display = 'flex';
    }
  }

  // 渲染文章列表
  function renderArticles(articles, offset) {
    articles.forEach((article, index) => {
      const articleEl = createArticleElement(article, offset + index);
      articlesList.appendChild(articleEl);
    });
  }

  // 创建文章元素
  function createArticleElement(article, index) {
    const div = document.createElement('div');
    div.className = 'article-item new-item';
    div.style.setProperty('--delay', (index * 0.05) + 's');

    const avatar = article.avatar || config.errorImg;
    const date = formatDate(article.created);

    div.innerHTML = `
      <div class="article-image" data-author="${escapeHtml(article.author)}" data-avatar="${escapeHtml(avatar)}" data-link="${escapeHtml(article.link)}">
        <img src="${escapeHtml(avatar)}" alt="${escapeHtml(article.author)}" onerror="this.src='${config.errorImg}'" />
      </div>
      <div class="article-container gradient-card" data-link="${escapeHtml(article.link)}">
        <div class="article-author">${escapeHtml(article.author)}</div>
        <div class="article-title">${escapeHtml(article.title)}</div>
        <div class="article-date">${escapeHtml(date)}</div>
      </div>
    `;

    // 绑定点击事件
    const imageEl = div.querySelector('.article-image');
    const containerEl = div.querySelector('.article-container');

    imageEl.addEventListener('click', function() {
      showAuthorArticles(
        this.dataset.author,
        this.dataset.avatar,
        this.dataset.link
      );
    });

    containerEl.addEventListener('click', function() {
      window.open(this.dataset.link, '_blank', 'noopener,noreferrer');
    });

    return div;
  }

  // 显示随机文章
  function displayRandomArticle() {
    if (allArticles.length === 0) return;

    const randomIndex = Math.floor(Math.random() * allArticles.length);
    randomArticle = allArticles[randomIndex];

    randomAuthor.textContent = randomArticle.author;
    randomTitle.textContent = randomArticle.title;
    randomDate.textContent = randomArticle.created;
    randomLink.href = randomArticle.link;
  }

  // 显示作者文章模态框
  function showAuthorArticles(author, avatar, link) {
    const modalAvatar = document.getElementById('modal-author-avatar');
    const modalNameLink = document.getElementById('modal-author-name-link');
    const modalArticles = document.getElementById('modal-articles-container');
    const modalBg = document.getElementById('modal-bg');

    modalAvatar.src = avatar || config.errorImg;
    modalAvatar.onerror = function() { this.src = config.errorImg; };

    modalNameLink.textContent = author;
    modalNameLink.href = new URL(link).origin;

    modalBg.src = avatar || config.errorImg;
    modalBg.onerror = function() { this.src = config.errorImg; };

    // 获取该作者的文章
    const authorArticles = allArticles
      .filter(a => a.author === author)
      .slice(0, 4);

    modalArticles.innerHTML = authorArticles.map((article, index) => `
      <div class="modal-article" style="--delay: ${index * 0.1}s">
        <a class="modal-article-title" href="${escapeHtml(article.link)}" target="_blank" rel="noopener noreferrer">
          ${escapeHtml(article.title)}
        </a>
        <div class="modal-article-date">📅 ${escapeHtml(formatDate(article.created))}</div>
      </div>
    `).join('');

    modal.classList.add('modal-open');
    document.body.style.overflow = 'hidden';
  }

  // 隐藏模态框
  function hideModal() {
    modal.classList.remove('modal-open');
    document.body.style.overflow = '';
  }

  // 格式化日期
  function formatDate(dateString) {
    if (!dateString) return '';
    return dateString.substring(0, 10);
  }

  // HTML 转义
  function escapeHtml(text) {
    if (typeof text !== 'string') return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // 启动
  init();
})();
</script>

<?php $this->need('footer.php'); ?>
