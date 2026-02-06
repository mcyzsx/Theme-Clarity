// 关于页面滚动动画 - 复刻 Myxz_Blog_Nuxt
(function() {
    'use strict';
    
    // 文字滚动切换动画
    var pursuitInterval = null;
    
    function initPursuitAnimation() {
        var aboutPage = document.getElementById('about-page');
        if (!aboutPage) return;
        
        pursuitInterval = setInterval(function() {
            var show = document.querySelector('.mask span[data-show]');
            var mask = document.querySelector('.mask');
            
            if (!show || !mask) return;
            
            var next = show.nextElementSibling || mask.querySelector('.first-tips');
            var up = document.querySelector('.mask span[data-up]');
            
            if (up) {
                up.removeAttribute('data-up');
            }
            
            show.removeAttribute('data-show');
            show.setAttribute('data-up', '');
            
            if (next) {
                next.setAttribute('data-show', '');
            }
        }, 2000);
    }
    
    // 页面加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPursuitAnimation);
    } else {
        initPursuitAnimation();
    }
    
    // 页面卸载时清除定时器
    window.addEventListener('beforeunload', function() {
        if (pursuitInterval) {
            clearInterval(pursuitInterval);
        }
    });
})();
