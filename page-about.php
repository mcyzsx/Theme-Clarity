<?php
/**
 * 关于页面
 *
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

$this->need('header.php');

// 获取关于页面设置
$aboutTitle = trim(clarity_opt('about_title', '关于本站'));
$aboutAvatar = trim(clarity_opt('about_avatar', ''));
if (empty($aboutAvatar)) {
    $aboutAvatar = clarity_site_logo(\Typecho\Common::url('assets/images/logo.svg', $this->options->themeUrl));
}

// 左侧标签 - 每行一个
$aboutLeftTagsRaw = trim(clarity_opt('about_left_tags', "💻 热爱编程\n📝 喜欢写作\n🎮 游戏玩家\n📚 终身学习"));
$aboutLeftTags = [];
foreach (explode("\n", $aboutLeftTagsRaw) as $line) {
    $line = trim($line);
    if ($line !== '') {
        $aboutLeftTags[] = ['text' => $line];
    }
}

// 右侧标签 - 每行一个
$aboutRightTagsRaw = trim(clarity_opt('about_right_tags', "乐观 积极 向上\n专注 坚持 创新\n分享 交流 成长\n感恩 包容 开放"));
$aboutRightTags = [];
foreach (explode("\n", $aboutRightTagsRaw) as $line) {
    $line = trim($line);
    if ($line !== '') {
        $aboutRightTags[] = ['text' => $line];
    }
}

// 问候语设置
$aboutHelloTitle1 = trim(clarity_opt('about_hello_title1', '你好，很高兴认识你👋'));
$aboutHelloTitle2 = trim(clarity_opt('about_hello_title2', '我叫'));
$aboutHelloName = trim(clarity_opt('about_hello_name', ''));
if (empty($aboutHelloName)) {
    $aboutHelloName = $this->options->title;
}
$aboutHelloContent1 = trim(clarity_opt('about_hello_content1', '是一名'));
$aboutHelloContent2 = trim(clarity_opt('about_hello_content2', '博主'));

// 站点介绍
$aboutTips = trim(clarity_opt('about_tips', '追求'));
$aboutConnect1 = trim(clarity_opt('about_connect1', '源于'));
$aboutConnect2 = trim(clarity_opt('about_connect2', '热爱而去'));
$aboutInlineWord = trim(clarity_opt('about_inline_word', '感受'));

// 滚动文字 - 每行一个
$aboutMaskWordsRaw = trim(clarity_opt('about_mask_words', "学习\n生活\n程序\n体验"));
$aboutMaskWords = [];
foreach (explode("\n", $aboutMaskWordsRaw) as $line) {
    $line = trim($line);
    if ($line !== '') {
        $aboutMaskWords[] = ['text' => $line];
    }
}

// 座右铭
$aboutMaximTip = trim(clarity_opt('about_maxim_tip', '座右铭'));
$aboutMaximTitle1 = trim(clarity_opt('about_maxim_title1', '生活明朗，'));
$aboutMaximTitle2 = trim(clarity_opt('about_maxim_title2', '万物可爱。'));

// 游戏
$aboutGameTip = trim(clarity_opt('about_game_tip', '爱好游戏'));
$aboutGameTitle = trim(clarity_opt('about_game_title', '原神'));
$aboutGameUid = trim(clarity_opt('about_game_uid', 'UID: 123456789'));

// 技能 - JSON 格式
$aboutSkillsRaw = clarity_opt('about_skills', '[{"name":"Vue","color":"#b8f0ae","icon":"https://api.iconify.design/logos:vue.svg"},{"name":"JavaScript","color":"#f7cb4f","icon":"https://api.iconify.design/logos:javascript.svg"},{"name":"CSS","color":"#2c51db","icon":"https://api.iconify.design/logos:css-3.svg"},{"name":"PHP","color":"#777bb4","icon":"https://api.iconify.design/logos:php.svg"},{"name":"Typecho","color":"#467b96","icon":""},{"name":"Node.js","color":"#333333","icon":"https://api.iconify.design/logos:nodejs-icon.svg"}]');
$aboutSkills = [];
if (!empty($aboutSkillsRaw)) {
    $decoded = json_decode($aboutSkillsRaw, true);
    if (is_array($decoded)) {
        $aboutSkills = $decoded;
    }
}
?>

<link rel="stylesheet" href="<?php $this->options->themeUrl('assets/css/about.css'); ?>?v=<?php echo CLARITY_VERSION; ?>">

<main id="main" class="main" role="main">
    <div id="about-page" class="page-content">
        <h1 class="author-title"><?php echo htmlspecialchars($aboutTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
        
        <div class="author-page-content">
            <!-- 作者头像区域 -->
            <div class="author-content">
                <div class="author-content-item" style="width: 100%;">
                    <div class="author-info">
                        <!-- 左侧标签 -->
                        <div class="author-tag-left">
                            <?php foreach ($aboutLeftTags as $index => $tag): ?>
                                <?php if ($index < 4): ?>
                                    <span class="author-tag"><?php echo htmlspecialchars($tag['text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- 头像 -->
                        <div class="author-img">
                            <img src="<?php echo htmlspecialchars($aboutAvatar, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($this->options->title, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        
                        <!-- 右侧标签 -->
                        <div class="author-tag-right">
                            <?php foreach ($aboutRightTags as $index => $tag): ?>
                                <?php if ($index < 4): ?>
                                    <span class="author-tag"><?php echo htmlspecialchars($tag['text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 问候语 -->
            <div class="author-content">
                <div class="author-content-item myInfoAndSayHello">
                    <div class="title1"><?php echo htmlspecialchars($aboutHelloTitle1, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="title2">
                        <?php echo htmlspecialchars($aboutHelloTitle2, ENT_QUOTES, 'UTF-8'); ?>
                        <span class="inline-word"><?php echo htmlspecialchars($aboutHelloName, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="title1">
                        <?php echo htmlspecialchars($aboutHelloContent1, ENT_QUOTES, 'UTF-8'); ?>
                        <span class="inline-word"><?php echo htmlspecialchars($aboutHelloContent2, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- 站点介绍和座右铭 -->
            <div class="author-content">
                <!-- 站点介绍 -->
                <div class="author-content-item aboutsiteTips">
                    <div class="author-content-item-tips"><?php echo htmlspecialchars($aboutTips, ENT_QUOTES, 'UTF-8'); ?></div>
                    <h2>
                        <?php echo htmlspecialchars($aboutConnect1, ENT_QUOTES, 'UTF-8'); ?>
                        <br>
                        <?php echo htmlspecialchars($aboutConnect2, ENT_QUOTES, 'UTF-8'); ?>
                        <span class="inline-word"><?php echo htmlspecialchars($aboutInlineWord, ENT_QUOTES, 'UTF-8'); ?></span>
                        <div class="mask">
                            <?php foreach ($aboutMaskWords as $index => $word): ?>
                                <span class="<?php echo $index === 0 ? 'first-tips' : ''; ?>" <?php echo $index === 0 ? 'data-show' : ''; ?>>
                                    <?php echo htmlspecialchars($word['text'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </h2>
                </div>
                
                <!-- 座右铭 -->
                <div class="author-content-item maxim">
                    <div class="author-content-item-tips"><?php echo htmlspecialchars($aboutMaximTip, ENT_QUOTES, 'UTF-8'); ?></div>
                    <span class="maxim-title">
                        <span style="opacity: 0.6; margin-bottom: 8px;"><?php echo htmlspecialchars($aboutMaximTitle1, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span><?php echo htmlspecialchars($aboutMaximTitle2, ENT_QUOTES, 'UTF-8'); ?></span>
                    </span>
                </div>
            </div>
            
            <!-- 游戏和技能 -->
            <div class="author-content">
                <!-- 游戏 -->
                <div class="author-content-item game">
                    <div class="card-content">
                        <div class="author-content-item-tips"><?php echo htmlspecialchars($aboutGameTip, ENT_QUOTES, 'UTF-8'); ?></div>
                        <span class="author-content-item-title"><?php echo htmlspecialchars($aboutGameTitle, ENT_QUOTES, 'UTF-8'); ?></span>
                        <div class="content-bottom">
                            <div class="tips"><?php echo htmlspecialchars($aboutGameUid, ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- 技能 - 带图标 -->
                <div class="author-content-item skill-main">
                    <div class="author-content-item-tips">技能</div>
                    <div class="skill-list">
                        <?php foreach ($aboutSkills as $skill): ?>
                            <div class="skill-item">
                                <?php if (!empty($skill['icon'])): ?>
                                    <div class="skill-icon" style="background: <?php echo htmlspecialchars($skill['color'] ?? '#e2e8f0', ENT_QUOTES, 'UTF-8'); ?>">
                                        <img src="<?php echo htmlspecialchars($skill['icon'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($skill['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                <?php endif; ?>
                                <span class="skill-name"><?php echo htmlspecialchars($skill['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- 页面内容 -->
            <div class="author-content">
                <div class="author-content-item" style="width: 100%;">
                    <article class="post-content">
                        <?php $this->content(); ?>
                    </article>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- 评论区域 -->
<section class="comments-section">
    <div class="comments-container">
        <?php $this->need('comments.php'); ?>
    </div>
</section>

<script src="<?php $this->options->themeUrl('assets/js/about.js'); ?>?v=<?php echo CLARITY_VERSION; ?>"></script>

<?php $this->need('footer.php'); ?>
