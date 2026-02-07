<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
$logoFallback = \Typecho\Common::url('assets/images/logo.svg', $this->options->themeUrl);
$logo = clarity_site_logo($logoFallback);
$showTitle = clarity_bool(clarity_opt('show_title', '1'));
$subtitle = trim((string) clarity_opt('subtitle', $this->options->description));
$emojiTail = trim((string) clarity_opt('emoji_tail', ''));
$menuItems = clarity_menu_items();
$menuIconInvert = clarity_bool(clarity_opt('menu_icon_invert', '0'));
$socialItems = clarity_json_option('social_json', []);
$userAuth = clarity_bool(clarity_opt('user_auth', '0'));
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
  if (preg_match('/\\bph\\s+ph-([a-z0-9-]+)\\b/i', $icon, $match)) {
    $name = strtolower($match[1]);
    $iconName = 'ph:' . $name;
    $iconUrl = 'https://api.iconify.design/ph/' . rawurlencode($name) . '.svg';
    return '<span class="iconify-mask ' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '" data-icon="' . htmlspecialchars($iconName, ENT_QUOTES, 'UTF-8') . '" style="--icon-url:url(\'' . htmlspecialchars($iconUrl, ENT_QUOTES, 'UTF-8') . '\')"></span>';
  }
  if (preg_match('/^(https?:)?\\//i', $icon) || preg_match('/^\\.\\//', $icon) || preg_match('/^\\.\\.\\//', $icon)) {
    return '<img src="' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '" alt="" />';
  }
  return '<span class="' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '"></span>';
};
?>
<aside id="z-sidebar" class="<?php echo $menuIconInvert ? 'menu-icon-invert' : ''; ?>">
  <div class="clarity-header">
    <?php if ($emojiTail !== ''): ?>
      <div class="emoji-tail">
        <?php
        $emojis = array_filter(array_map('trim', explode(',', $emojiTail)));
        foreach ($emojis as $index => $emoji):
        ?>
          <span class="split-char" style="--delay: <?php echo ($index * 0.6 - 3); ?>s"><?php echo $emoji; ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <a href="<?php echo $this->options->siteUrl; ?>">
      <?php if ($logo): ?>
        <img src="<?php echo htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>" class="clarity-logo<?php echo $showTitle ? ' circle' : ''; ?>" alt="<?php echo htmlspecialchars($this->options->title, ENT_QUOTES, 'UTF-8'); ?>" />
      <?php endif; ?>
    </a>

    <?php if ($showTitle): ?>
      <div class="clarity-text">
        <div class="header-title">
          <?php
          $chars = preg_split('//u', $this->options->title, -1, PREG_SPLIT_NO_EMPTY);
          foreach ($chars as $idx => $char):
          ?>
            <span class="split-char" style="--delay: <?php echo (($idx + 1) * 0.1); ?>s"><?php echo htmlspecialchars($char, ENT_QUOTES, 'UTF-8'); ?></span>
          <?php endforeach; ?>
        </div>
        <div class="header-subtitle"><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></div>
      </div>
    <?php endif; ?>
  </div>

  <nav class="sidebar-nav scrollcheck-y">
    <div class="search-btn sidebar-nav-item gradient-card" onclick="SearchWidget.open()">
      <span class="icon-[ph--magnifying-glass-bold]"></span>
      <span class="nav-text">搜索</span>
      <kbd class="search-shortcut">Ctrl+K</kbd>
    </div>

    <?php if (!empty($menuItems)): ?>
      <menu>
        <?php foreach ($menuItems as $item): ?>
          <?php
          $children = isset($item['children']) && is_array($item['children']) ? $item['children'] : [];
          $hasChildren = !empty($children);
          $url = $item['url'] ?? '#';
          $text = $item['text'] ?? '';
          $icon = $item['icon'] ?? '';
          $target = $item['target'] ?? '';
          ?>
          <li class="<?php echo $hasChildren ? 'has-submenu' : ''; ?>">
            <?php if ($hasChildren): ?>
              <a class="sidebar-nav-item has-dropdown" href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $target ? ' target="' . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
                <?php if ($icon): ?>
                  <?php echo $renderIcon($icon); ?>
                <?php endif; ?>
                <span class="nav-text"><?php echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="icon-[ph--caret-down-bold] dropdown-arrow"></span>
              </a>
              <div class="dropdown-menu">
                <?php foreach ($children as $child): ?>
                  <?php
                  $childUrl = $child['url'] ?? '#';
                  $childText = $child['text'] ?? '';
                  $childIcon = $child['icon'] ?? '';
                  $childTarget = $child['target'] ?? '';
                  ?>
                  <a class="dropdown-item" href="<?php echo htmlspecialchars($childUrl, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $childTarget ? ' target="' . htmlspecialchars($childTarget, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
                    <?php if ($childIcon): ?>
                      <?php echo $renderIcon($childIcon); ?>
                    <?php endif; ?>
                    <span class="nav-text"><?php echo htmlspecialchars($childText, ENT_QUOTES, 'UTF-8'); ?></span>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <a class="sidebar-nav-item" href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $target ? ' target="' . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
                <?php if ($icon): ?>
                  <?php echo $renderIcon($icon); ?>
                <?php endif; ?>
                <span class="nav-text"><?php echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php if ($target === '_blank'): ?>
                  <span class="icon-[ph--arrow-up-right] external-tip"></span>
                <?php endif; ?>
              </a>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </menu>
    <?php endif; ?>
  </nav>

  <footer class="sidebar-footer">
    <?php if ($userAuth): ?>
      <?php if ($this->user->hasLogin()): ?>
        <?php
        $userName = '';
        if (isset($this->user->screenName)) {
            $userName = (string) $this->user->screenName;
        }
        if ($userName === '' && isset($this->user->name)) {
            $userName = (string) $this->user->name;
        }
        if ($userName === '' && isset($this->user->mail)) {
            $userName = (string) $this->user->mail;
        }
        if ($userName === '') {
            $userName = '用户';
        }
        $userMail = isset($this->user->mail) ? (string) $this->user->mail : '';
        $avatar = '';
        if ($userMail !== '') {
            $avatar = \Typecho\Common::gravatarUrl($userMail, 64, 'X', 'mp', isset($_SERVER['HTTPS']));
        }
        $roleMap = [
            'administrator' => '管理员',
            'editor' => '编辑',
            'contributor' => '贡献者',
            'subscriber' => '订阅者',
        ];
        $role = isset($this->user->group) ? (string) $this->user->group : '';
        $roleLabel = $roleMap[$role] ?? '已登录';
        ?>
        <a class="user-entry" href="<?php echo $this->options->adminUrl; ?>">
          <div class="avatar-wrapper">
            <?php if ($avatar !== ''): ?>
              <img class="user-avatar" src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" />
            <?php else: ?>
              <span class="icon-[ph--user-circle-bold] default-avatar-icon"></span>
            <?php endif; ?>
          </div>
          <div class="user-info">
            <span class="user-name"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
            <span class="user-desc"><?php echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?></span>
          </div>
        </a>
      <?php else: ?>
        <a class="user-entry" href="<?php echo $this->options->adminUrl; ?>" data-auth-trigger="sidebar-login">
          <div class="avatar-wrapper">
            <span class="icon-[ph--user-circle-bold] default-avatar-icon"></span>
          </div>
          <div class="user-info">
            <span class="user-name">登录</span>
            <span class="user-desc">点击登录账号</span>
          </div>
        </a>
      <?php endif; ?>
    <?php endif; ?>

    <div class="particles-toggle" style="display: flex; justify-content: center; margin-bottom: 16px;">
      <button id="particles-toggle-btn" class="z-btn" title="切换粒子效果">
        <span class="icon-[lets-icons--on]"></span>
        <span>粒子效果</span>
      </button>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('particles-toggle-btn');
        if (toggleBtn) {
          // 处理图标显示
          const iconElement = toggleBtn.querySelector('span:first-child');
          if (iconElement) {
            // 使用 iconify API 加载图标
            const updateIcon = function(isEnabled) {
              const iconName = isEnabled ? 'lets-icons:on' : 'lets-icons:off';
              const iconUrl = 'https://api.iconify.design/' + iconName.replace(':', '/') + '.svg';
              iconElement.className = isEnabled ? 'icon-[lets-icons--on]' : 'icon-[lets-icons--off]';
              iconElement.style.setProperty('--icon-url', 'url("' + iconUrl + '")');
              iconElement.classList.add('iconify-mask');
            };
            
            // 初始化图标状态
            if (typeof getParticlesState === 'function') {
              updateIcon(getParticlesState());
            }
            
            // 绑定点击事件
            toggleBtn.onclick = function() {
              if (typeof toggleParticles === 'function') {
                const newState = toggleParticles();
                updateIcon(newState);
              }
            };
          }
        }
      });
    </script>

    <div class="theme-toggle" x-data="themeToggle()">
      <button :class="{ active: theme === 'light' }" @click="setTheme('light', $event)" title="浅色模式">
        <span class="icon-[ph--sun-bold]"></span>
      </button>
      <button :class="{ active: theme === 'system' }" @click="setTheme('system', $event)" title="跟随系统">
        <span class="icon-[ph--monitor-bold]"></span>
      </button>
      <button :class="{ active: theme === 'dark' }" @click="setTheme('dark', $event)" title="深色模式">
        <span class="icon-[ph--moon-bold]"></span>
      </button>
    </div>

    <?php if (!empty($socialItems)): ?>
      <div class="social-icons">
        <?php foreach ($socialItems as $item): ?>
          <?php
          $url = $item['url'] ?? '#';
          $text = $item['text'] ?? '';
          $icon = $item['icon'] ?? '';
          ?>
          <a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
            <?php if ($icon): ?>
              <?php echo $renderIcon($icon); ?>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </footer>
  
  <style>
  .zhilu-header {
    position: relative;
    padding: 1.5rem;
    margin: 1rem 0;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--c-bg-soft) 0%, var(--c-bg-card) 100%);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--c-border);
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    animation: gentleBackgroundShift 20s ease-in-out infinite;
    will-change: transform, box-shadow, background;
    transform: translateZ(0);
  }
  
  @keyframes gentleBackgroundShift {
    0%, 100% {
      background: linear-gradient(135deg, var(--c-bg-soft) 0%, var(--c-bg-card) 100%);
    }
    25% {
      background: linear-gradient(135deg, var(--c-bg-soft) 0%, rgba(44, 142, 221, 0.08) 40%, var(--c-bg-card) 100%);
    }
    50% {
      background: linear-gradient(135deg, var(--c-bg-soft) 0%, rgba(44, 142, 221, 0.12) 50%, var(--c-bg-card) 100%);
    }
    75% {
      background: linear-gradient(135deg, var(--c-bg-soft) 0%, rgba(44, 142, 221, 0.06) 60%, var(--c-bg-card) 100%);
    }
  }
  
  .zhilu-header::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 30%, rgba(44, 142, 221, 0.15) 0%, transparent 60%),
                radial-gradient(circle at 80% 70%, rgba(44, 142, 221, 0.12) 0%, transparent 60%),
                radial-gradient(circle at 40% 80%, rgba(44, 142, 221, 0.1) 0%, transparent 60%);
    opacity: 0.8;
    transition: opacity 0.4s ease;
    z-index: 0;
    pointer-events: none;
  }
  
  .zhilu-header:hover::after {
    opacity: 1;
  }
  
  @keyframes floatParticles {
    0%, 100% {
      transform: translate(0, 0) scale(1);
      opacity: 0.6;
    }
    25% {
      transform: translate(30px, -25px) scale(1.5);
      opacity: 1;
    }
    50% {
      transform: translate(-25px, 20px) scale(0.7);
      opacity: 0.7;
    }
    75% {
      transform: translate(25px, 30px) scale(1.4);
      opacity: 1;
    }
  }
  
  .zhilu-header .header-particle-bg {
    position: absolute;
    background: linear-gradient(45deg, rgba(44, 142, 221, 0.3), rgba(44, 142, 221, 0.15));
    border-radius: 50%;
    animation: floatParticles 10s ease-in-out infinite;
    z-index: 0;
    filter: blur(1px);
    box-shadow: 0 0 12px rgba(44, 142, 221, 0.2);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
  }
  
  .zhilu-header:hover .header-particle-bg {
    animation-duration: 6s;
    filter: blur(1.5px);
    box-shadow: 0 0 16px rgba(44, 142, 221, 0.3);
  }
  
  .zhilu-header .header-particle-bg:nth-child(1) {
    width: 10px;
    height: 10px;
    top: 20%;
    left: 15%;
    animation-delay: 0s;
    background: linear-gradient(45deg, rgba(44, 142, 221, 0.4), rgba(44, 142, 221, 0.25));
  }
  
  .zhilu-header .header-particle-bg:nth-child(2) {
    width: 4px;
    height: 4px;
    top: 65%;
    left: 75%;
    animation-delay: 3s;
    background: linear-gradient(135deg, rgba(44, 142, 221, 0.2), rgba(44, 142, 221, 0.1));
  }
  
  .zhilu-header .header-particle-bg:nth-child(3) {
    width: 12px;
    height: 12px;
    top: 40%;
    left: 60%;
    animation-delay: 4s;
    background: linear-gradient(225deg, rgba(44, 142, 221, 0.45), rgba(44, 142, 221, 0.3));
  }
  
  .zhilu-header .header-particle-bg:nth-child(4) {
    width: 10px;
    height: 10px;
    top: 55%;
    left: 35%;
    animation-delay: 6s;
    background: linear-gradient(315deg, rgba(44, 142, 221, 0.4), rgba(44, 142, 221, 0.25));
  }
  
  .zhilu-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    border-color: var(--c-border);
  }
  
  .header-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .avatar-container {
    flex-shrink: 0;
    position: relative;
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid var(--c-border);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  
  .zhilu-header:hover .avatar-container {
    transform: scale(1.08);
    border-color: var(--c-primary);
    box-shadow: 0 6px 16px rgba(44, 142, 221, 0.2);
  }
  
  .avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .text-container {
    flex-grow: 1;
    overflow: visible;
    min-width: 0;
  }
  
  .name {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
    color: var(--c-text-1);
    white-space: nowrap;
    overflow: visible;
    transition: color 0.3s ease;
    font-family: 'Inter', 'PingFang SC', 'Microsoft YaHei', sans-serif;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
  }
  
  .zhilu-header:hover .name {
    color: var(--c-primary);
  }
  
  .tagline {
    font-size: 0.875rem;
    color: var(--c-text-2);
    margin: 0.5rem 0 0;
    opacity: 0.9;
    transition: all 0.3s ease;
    line-height: 1.4;
  }
  
  .zhilu-header:hover .tagline {
    opacity: 1;
    color: var(--c-text-1);
  }
  
  .moyu {
    color: #c79f2c;
    font-weight: 600;
  }
  
  .love {
    color: #fa6b81;
    font-weight: 600;
  }
  
  .tag-transition,
  .subtitle-transition {
    transition: all 0.3s ease;
  }
  
  .zhilu-header.header-hover-effect .tag-transition.tag-hover,
  .zhilu-header.header-hover-effect .subtitle-transition.subtitle-hover {
    display: none;
  }
  
  .zhilu-header.header-hover-effect.hovered .tag-transition.tag-default,
  .zhilu-header.header-hover-effect.hovered .subtitle-transition.subtitle-default {
    display: none;
  }
  
  .zhilu-header.header-hover-effect.hovered .tag-transition.tag-hover,
  .zhilu-header.header-hover-effect.hovered .subtitle-transition.subtitle-hover {
    display: inline;
  }
  
  .developer-tag {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--c-primary);
    background: rgba(44, 142, 221, 0.15);
    padding: 0 0.1rem;
    border-radius: 20px;
    margin-left: 0;
    margin-right: 0;
    vertical-align: middle;
    transition: all 0.3s ease;
    border: none;
    font-family: 'Inter', 'PingFang SC', sans-serif;
    display: inline-block;
    line-height: 1.2;
  }
  
  .zhilu-header:hover .developer-tag {
    background: rgba(44, 142, 221, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 2px 12px rgba(44, 142, 221, 0.25);
  }
  
  @media (max-width: 768px) {
    .zhilu-header {
      padding: 1.25rem;
      margin: 0.75rem 0;
    }
    
    .avatar-container {
      width: 3.5rem;
      height: 3.5rem;
    }
    
    .name {
      font-size: 1.25rem;
    }
    
    .tagline {
      font-size: 0.8rem;
    }
    
    .developer-tag {
      font-size: 0.7rem;
      padding: 0.2rem 0.6rem;
    }
  }
  
  @media (max-width: 480px) {
    .zhilu-header {
      padding: 1rem;
      animation: none;
    }
    
    .header-content {
      gap: 1rem;
    }
    
    .avatar-container {
      width: 3rem;
      height: 3rem;
    }
    
    .name {
      font-size: 1.1rem;
    }
    
    .tagline {
      font-size: 0.75rem;
    }
    
    .zhilu-header .header-particle-bg {
      animation-duration: 16s;
      opacity: 0.1;
    }
    
    .zhilu-header:hover .header-particle-bg {
      animation-duration: 12s;
    }
  }
  
  @media (prefers-reduced-motion: reduce) {
    .zhilu-header {
      animation: none;
    }
    
    .zhilu-header .header-particle-bg {
      animation: none;
      opacity: 0.05;
    }
    
    .zhilu-header::before,
    .zhilu-header::after {
      transition: none;
      animation: none;
    }
  }
  
  /* Sidebar Navigation Styles */
  .sidebar-nav {
    flex-grow: 1;
    padding: 0 5%;
    font-size: 0.9em;
  }
  
  .sidebar-nav h3 {
    margin: 2em 0 1em 1em;
    font: inherit;
    color: var(--c-text-2);
  }
  
  .sidebar-nav li {
    margin: 0.5em 0;
  }
  
  .sidebar-nav menu {
    margin: 0;
    padding: 0;
    list-style: none;
  }
  
  .sidebar-nav-item {
    display: flex;
    align-items: center;
    gap: 0.625em;
    padding: 0.75em 1.25em;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, var(--c-bg-soft) 0%, var(--c-bg-card) 100%);
    border: 1px solid var(--c-border);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
    color: var(--c-text-2);
    text-decoration: none;
  }
  
  .sidebar-nav-item:hover {
    background: linear-gradient(135deg, rgba(var(--c-primary-rgb, 59, 130, 246), 0.1) 0%, rgba(var(--c-primary-rgb, 59, 130, 246), 0.05) 100%);
    color: var(--c-text);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(var(--c-primary-rgb, 59, 130, 246), 0.1);
  }
  
  .sidebar-nav-item::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(var(--c-primary-rgb, 59, 130, 246), 0.1), transparent);
    transition: left 0.6s ease;
  }
  
  .sidebar-nav-item:hover::before {
    left: 100%;
  }
  
  .sidebar-nav-item.active {
    background: linear-gradient(135deg, rgba(var(--c-primary-rgb, 59, 130, 246), 0.15) 0%, rgba(var(--c-primary-rgb, 59, 130, 246), 0.08) 100%);
    color: var(--c-primary);
    box-shadow: 0 2px 8px rgba(var(--c-primary-rgb, 59, 130, 246), 0.15);
    font-weight: 600;
    position: relative;
    overflow: hidden;
  }
  
  .sidebar-nav-item.active::before {
    content: "";
    position: absolute;
    left: 0;
    width: 3px;
    height: 100%;
    background: var(--c-primary);
    transform: scaleY(1);
    transform-origin: center center;
    transition: transform 0.3s ease-out;
  }
  
  .sidebar-nav-item.active::after {
    content: "•";
    width: 1em;
    text-align: center;
    color: var(--c-primary);
    font-weight: bold;
    font-size: 1.4em;
    margin-left: 2px;
  }
  
  .sidebar-nav-item:not(.active)::before {
    transform: scaleY(0);
    transform-origin: center center;
    transition: transform 0.2s ease-in;
  }
  
  .sidebar-nav-item .iconify {
    font-size: 1.5em;
  }
  
  .sidebar-nav-item .nav-text {
    flex-grow: 1;
  }
  
  .sidebar-nav-item .external-tip {
    opacity: 0.5;
    font-size: 1em;
  }
  
  .sidebar-nav-item .dropdown-arrow {
    font-size: 0.8em;
    opacity: 0.6;
  }
  
  /* Search Button */
  .search-btn {
    margin: 1rem 0;
    outline: 1px solid var(--c-border);
    outline-offset: -1px;
    cursor: text;
  }
  
  .search-btn:hover {
    outline-color: transparent;
    background-color: transparent;
  }
  
  .search-btn .nav-text {
    opacity: 0.5;
  }
  
  .search-shortcut {
    opacity: 0.5;
    padding: 0 0.2em;
    border-radius: 0.2em;
    background-color: var(--c-bg-soft);
    font-size: 0.8em;
  }
  
  /* Dropdown Menu */
  .dropdown-menu {
    display: none;
    margin-left: 1em;
    padding-left: 1em;
    border-left: 1px solid var(--c-border);
  }
  
  .has-submenu .dropdown-menu {
    display: block;
  }
  
  .dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.5em;
    padding: 0.5em 1em;
    border-radius: 8px;
    color: var(--c-text-2);
    text-decoration: none;
    transition: all 0.2s ease;
  }
  
  .dropdown-item:hover {
    background: var(--c-bg-soft);
    color: var(--c-text);
  }
  
  /* Sidebar Footer */
  .sidebar-footer {
    --gap: clamp(0.5rem, 3vh, 1rem);
    display: grid;
    gap: var(--gap);
    padding: var(--gap);
    font-size: 0.8em;
    text-align: center;
    color: var(--c-text-2);
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .sidebar-nav {
      padding: 0 3%;
    }
  }
  </style>
</aside>
