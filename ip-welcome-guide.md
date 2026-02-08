# Clarity 主题 IP 地理位置欢迎组件配置指南

## 功能介绍

Clarity 主题的 **访客信息** 组件可以根据访客的 IP 地址自动识别地理位置，并显示个性化的欢迎语。效果如下：

```
访客信息
━━━━━━━━━━━━━━━━━━━
欢迎来自姑苏的朋友 💖
🌙 晚上好，夜生活嗨起来
Tip：园林之城，精致典雅
```

## 实现原理

1. 使用 `https://myip.ipip.net/json` API 获取访客的 IP 和地理位置
2. 根据地理位置匹配预设的城市欢迎语
3. 根据当前时段显示不同的问候语
4. 前端实时渲染个性化欢迎信息

## 配置步骤

### 1. 启用组件

进入 Typecho 后台 → 控制台 → 外观 → 设置外观：

- 找到 **"右侧边栏组件顺序"** 设置项
- 在输入框中添加 `welcome`（建议放在第一行，显示在侧边栏顶部）
- 保存设置

### 2. 配置选项

在主题设置页面中，找到以下选项：

#### 欢迎组件选项
- **显示访客地理位置欢迎语**：勾选此项启用 IP 欢迎功能

#### 城市欢迎语配置（JSON 格式）

默认已内置 30+ 个城市的欢迎语，格式如下：

```json
{
  "default": ["欢迎来自{city}的朋友", "带我去你的城市逛逛吧"],
  "北京": ["欢迎来自首都的朋友", "京城风光，值得一游"],
  "上海": ["欢迎来自魔都的朋友", "外滩夜景，美不胜收"],
  "苏州": ["欢迎来自姑苏的朋友", "园林之城，精致典雅"]
}
```

**格式说明**：
- `default`：默认欢迎语，当访客城市不在列表时使用，`{city}` 会被替换为实际城市名
- 其他城市： `"城市名": ["欢迎语", "提示语"]`

#### 时段问候语配置（JSON 格式）

```json
{
  "morning": ["早上好", "一日之计在于晨"],
  "noon": ["中午好", "记得按时吃饭"],
  "afternoon": ["下午好", "保持专注，继续加油"],
  "evening": ["晚上好", "夜生活嗨起来"],
  "night": ["夜深了", "早点休息，晚安"]
}
```

**时段划分**：
- `morning`：05:00 - 11:59
- `noon`：12:00 - 13:59
- `afternoon`：14:00 - 17:59
- `evening`：18:00 - 21:59
- `night`：22:00 - 04:59

---

## 核心代码解析

### 1. 主题设置注册（functions.php）

在 `functions.php` 中注册主题设置项：

```php
// 显示访客地理位置欢迎语选项
$welcomeShowIp = new \Typecho\Widget\Helper\Form\Element\Checkbox(
    'clarity_welcome_show_ip',
    ['1' => _t('显示访客地理位置欢迎语')],
    ['1'],
    _t('')
);
$form->addInput($welcomeShowIp);

// 城市欢迎语配置
$welcomeCityMessages = new \Typecho\Widget\Helper\Form\Element\Textarea(
    'clarity_welcome_city_messages',
    null,
    '{"default":["欢迎来自{city}的朋友","带我去你的城市逛逛吧"],"北京":["欢迎来自首都的朋友","京城风光，值得一游"],...}',
    _t('城市欢迎语配置'),
    _t('JSON 格式：{"城市名": ["欢迎语", "提示语"], "default": ["默认欢迎语", "默认提示"]}')
);
$form->addInput($welcomeCityMessages);

// 时段问候语配置
$welcomeTimeMessages = new \Typecho\Widget\Helper\Form\Element\Textarea(
    'clarity_welcome_time_messages',
    null,
    '{"morning":["早上好","一日之计在于晨"],"noon":["中午好","记得按时吃饭"],...}',
    _t('时段问候语配置'),
    _t('JSON 格式：{"morning": ["问候", "提示"], "noon": [...]}')
);
$form->addInput($welcomeTimeMessages);
```

### 2. 侧边栏组件渲染（aside.php）

在 `aside.php` 中添加组件渲染逻辑：

```php
case 'welcome': ?>
  <?php
  $uniqueId = uniqid();
  $welcomeShowIp = clarity_bool(clarity_opt('welcome_show_ip', '1'));
  $cityMessages = clarity_opt('welcome_city_messages', '');
  $timeMessages = clarity_opt('welcome_time_messages', '');
  ?>
  <section class="widget widget-welcome">
    <hgroup class="widget-title text-creative">访客信息</hgroup>
    <div class="widget-body widget-card welcome-card">
      <div class="welcome-content" id="welcome-v2-<?php echo $uniqueId; ?>">
        <div class="welcome-loading">正在定位中...</div>
      </div>
    </div>
    <?php if ($welcomeShowIp): ?>
      <script>
        // JavaScript 代码见下文
      </script>
    <?php endif; ?>
  </section>
```

### 3. JavaScript 核心逻辑

```javascript
(function() {
  const uniqueId = '<?php echo $uniqueId; ?>';
  const cityMessages = <?php echo $cityMessages ?: '{}'; ?>;
  const timeMessages = <?php echo $timeMessages ?: '{}'; ?>;

  // 根据小时数判断时段
  function getTimePeriod(hour) {
    if (hour >= 5 && hour < 12) return 'morning';
    if (hour >= 12 && hour < 14) return 'noon';
    if (hour >= 14 && hour < 18) return 'afternoon';
    if (hour >= 18 && hour < 22) return 'evening';
    return 'night';
  }

  // 获取时段对应的图标
  function getTimeIcon(period) {
    const icons = {
      morning: '☀️',
      noon: '🌤️',
      afternoon: '⛅',
      evening: '🌙',
      night: '🌙'
    };
    return icons[period] || '🌙';
  }

  // 渲染欢迎信息
  function renderWelcome(location) {
    const now = new Date();
    const hour = now.getHours();
    const period = getTimePeriod(hour);

    // 从 location 数组提取地理位置
    // location 格式: ["中国", "江苏", "苏州", "", "电信"]
    const province = location[1] || '';
    const city = location[2] || '';
    const cityName = city || province || '未知';

    // 获取城市欢迎语（优先级：城市 > 省份 > 默认）
    let cityWelcome = ['欢迎来自', cityName, '的小友'];
    let cityTip = '带我去你的城市逛逛吧！';

    if (cityMessages[cityName]) {
      cityWelcome = [cityMessages[cityName][0]];
      cityTip = cityMessages[cityName][1] || cityTip;
    } else if (cityMessages[province]) {
      cityWelcome = [cityMessages[province][0]];
      cityTip = cityMessages[province][1] || cityTip;
    } else if (cityMessages.default) {
      cityWelcome = [cityMessages.default[0].replace('{city}', cityName)];
      cityTip = cityMessages.default[1] || cityTip;
    }

    // 获取时段问候语
    let timeGreeting = '晚上好';
    let timeTip = '夜生活嗨起来！';
    if (timeMessages[period]) {
      timeGreeting = timeMessages[period][0];
      timeTip = timeMessages[period][1] || timeTip;
    }

    // 生成 HTML
    const html = '<div class="welcome-main">' +
      '<div class="welcome-city">' + cityWelcome.join('') + ' <span class="welcome-heart">💖</span></div>' +
      '<div class="welcome-time">' + getTimeIcon(period) + ' ' + timeGreeting + '，' + timeTip + '</div>' +
      '<div class="welcome-tip">Tip：' + cityTip + '</div>' +
      '</div>';

    const el = document.getElementById('welcome-v2-' + uniqueId);
    if (el) el.innerHTML = html;
  }

  // 调用 API 获取地理位置
  fetch('https://myip.ipip.net/json')
    .then(res => res.json())
    .then(data => {
      if (data.ret === 'ok' && data.data && data.data.location) {
        renderWelcome(data.data.location);
      } else {
        renderWelcome(['', '', '']);
      }
    })
    .catch(() => {
      renderWelcome(['', '', '']);
    });
})();
```

### 4. API 数据格式

`https://myip.ipip.net/json` 返回的数据格式：

```json
{
  "ret": "ok",
  "data": {
    "ip": "222.190.239.169",
    "location": ["中国", "江苏", "苏州", "", "电信"]
  }
}
```

**location 数组说明**：
- `[0]` - 国家
- `[1]` - 省份/直辖市
- `[2]` - 城市
- `[3]` - 区县（可能为空）
- `[4]` - 运营商

### 5. CSS 样式（custom.css）

```css
/* Welcome Widget Styles */
.widget-welcome .welcome-card {
  padding: 1rem;
}

.welcome-content {
  min-height: 80px;
}

.welcome-loading {
  color: var(--c-text-3);
  font-size: 0.875rem;
  text-align: center;
  padding: 1rem 0;
}

.welcome-main {
  display: flex;
  flex-direction: column;
  gap: 0.625rem;
}

.welcome-city {
  font-size: 0.9375rem;
  font-weight: 500;
  color: var(--c-text);
  line-height: 1.5;
}

/* 心形跳动动画 */
.welcome-city .welcome-heart {
  display: inline-block;
  animation: heartBeat 1.5s ease-in-out infinite;
}

@keyframes heartBeat {
  0%, 100% { transform: scale(1); }
  14% { transform: scale(1.1); }
  28% { transform: scale(1); }
  42% { transform: scale(1.1); }
}

.welcome-time {
  font-size: 0.875rem;
  color: var(--c-primary);
  font-weight: 500;
  line-height: 1.5;
}

.welcome-tip {
  font-size: 0.8125rem;
  color: var(--c-text-2);
  line-height: 1.5;
}

/* Dark mode adjustments */
.dark .welcome-city {
  color: var(--c-text);
}

.dark .welcome-time {
  color: var(--c-primary);
}

.dark .welcome-tip {
  color: var(--c-text-2);
}
```

---

## 自定义城市欢迎语

如果你想添加新的城市或修改现有欢迎语，按照以下步骤操作：

### 1. 确定城市名称

首先需要在浏览器控制台查看 API 返回的城市名：

```javascript
fetch('https://myip.ipip.net/json')
  .then(r => r.json())
  .then(data => console.log(data.data.location[2]));
```

### 2. 编辑配置

在后台主题设置中找到 **"城市欢迎语配置"**，添加新的城市条目：

```json
{
  "default": ["欢迎来自{city}的朋友", "带我去你的城市逛逛吧"],
  "北京": ["欢迎来自首都的朋友", "京城风光，值得一游"],
  "上海": ["欢迎来自魔都的朋友", "外滩夜景，美不胜收"],
  "苏州": ["欢迎来自姑苏的朋友", "园林之城，精致典雅"],
  "成都": ["欢迎来自蓉城的朋友", "天府之国，美食天堂"],
  "你的城市": ["欢迎来自xxx的朋友", "这里写提示语"]
}
```

**注意事项**：
- JSON 格式必须正确，注意逗号和引号
- 城市名要与 IP 库返回的城市名称完全一致
- 最后一个条目后面不要加逗号

### 3. 城市匹配优先级

代码中的匹配逻辑优先级：

1. **精确匹配城市名** - `cityMessages[cityName]`
2. **匹配省份名** - `cityMessages[province]`
3. **使用默认配置** - `cityMessages.default`

这意味着你可以：
- 为特定城市设置专属欢迎语
- 为整个省份设置通用欢迎语
- 使用 `{city}` 占位符动态插入城市名

---

## 内置城市列表

主题已预设以下城市的欢迎语：

### 直辖市
- 北京、上海、天津、重庆

### 副省级城市
- 深圳、杭州、南京、成都、武汉、西安
- 青岛、宁波、厦门、大连

### 省会城市
- 广州、长沙、郑州、济南、合肥、福州
- 昆明、贵阳、拉萨、兰州、西宁、银川
- 乌鲁木齐、哈尔滨、长春、沈阳
- 石家庄、太原、南昌、南宁、海口

### 旅游城市
- 三亚

### 港澳台
- 香港、澳门、台北

---

## 故障排查

### 欢迎信息不显示

1. 检查浏览器控制台是否有报错
2. 确认 `https://myip.ipip.net/json` 接口可以正常访问
3. 检查主题设置中 **"显示访客地理位置欢迎语"** 是否已勾选
4. 检查 `welcome` 是否已添加到侧边栏组件顺序中

### 城市匹配失败

1. 在浏览器控制台执行以下代码查看 API 返回：
   ```javascript
   fetch('https://myip.ipip.net/json')
     .then(r => r.json())
     .then(console.log)
   ```
2. 检查返回的 `location[2]`（城市名）是否与配置中的键名一致
3. 注意特殊字符和空格问题

### JSON 格式错误

如果配置保存后无法生效，可能是 JSON 格式有误。可以使用在线 JSON 校验工具检查：

```json
{
  "default": ["欢迎来自{city}的朋友", "带我去你的城市逛逛吧"],
  "北京": ["欢迎来自首都的朋友", "京城风光，值得一游"]
}
```

**常见错误**：
- 键名或值使用了中文引号 `"` 而不是英文引号 `"`
- 最后一个条目后面有多余的逗号
- 缺少逗号分隔不同的键值对

### 样式显示异常

1. 清除浏览器缓存
2. 检查 `custom.css` 是否正确加载
3. 确认没有其他 CSS 覆盖欢迎组件样式
4. 检查浏览器开发者工具中的元素样式

---

## 进阶自定义

### 修改时段划分

编辑 `aside.php` 中的 `getTimePeriod` 函数：

```javascript
function getTimePeriod(hour) {
  if (hour >= 6 && hour < 11) return 'morning';   // 早上 6-11点
  if (hour >= 11 && hour < 13) return 'noon';     // 中午 11-13点
  if (hour >= 13 && hour < 17) return 'afternoon'; // 下午 13-17点
  if (hour >= 17 && hour < 22) return 'evening';   // 晚上 17-22点
  return 'night';  // 深夜 22-6点
}
```

### 添加更多时段图标

```javascript
function getTimeIcon(period) {
  const icons = {
    morning: '🌅',   // 日出
    noon: '☀️',      // 太阳
    afternoon: '🌤️', // 多云
    evening: '🌆',   // 城市黄昏
    night: '🌃'      // 城市夜景
  };
  return icons[period] || '🌙';
}
```

### 自定义动画效果

修改 CSS 中的动画关键帧：

```css
@keyframes heartBeat {
  0%, 100% { 
    transform: scale(1); 
    filter: drop-shadow(0 0 0 transparent);
  }
  50% { 
    transform: scale(1.2); 
    filter: drop-shadow(0 0 8px rgba(255, 0, 100, 0.6));
  }
}
```

---

## 隐私说明

- 本功能仅在前端通过 JavaScript 获取 IP 地理位置
- 不会将访客 IP 信息存储到服务器或数据库
- 使用的 `ipip.net` API 为公开接口，仅返回大致地理位置
- 不收集任何个人身份信息

---

## 更新日志

- **2026-02-08**：新增 IP 地理位置欢迎组件，支持 30+ 城市欢迎语
  - 支持根据 IP 自动识别访客地理位置
  - 支持根据时段显示不同问候语
  - 支持自定义城市欢迎语
  - 支持自定义时段问候语
  - 内置心形跳动动画效果
