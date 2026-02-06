// 粒子效果实现 - 完全复刻 ATao-Blog 的样式
class ParticlesBackground {
  constructor() {
    this.canvas = null;
    this.ctx = null;
    this.particles = [];
    this.animationId = null;
    this.isActive = false;
    this.isDark = false;
    this.colors = [];
    this.config = {
      particleCount: 35
    };
    
    this.init();
  }

  init() {
    this.canvas = document.createElement('canvas');
    this.canvas.id = 'particles-canvas';
    this.canvas.style.position = 'fixed';
    this.canvas.style.top = '0';
    this.canvas.style.left = '0';
    this.canvas.style.width = '100%';
    this.canvas.style.height = '100%';
    this.canvas.style.zIndex = '9999';
    this.canvas.style.pointerEvents = 'none';
    document.body.appendChild(this.canvas);

    this.ctx = this.canvas.getContext('2d');
    if (!this.ctx) return;

    this.isDark = document.documentElement.classList.contains('dark');
    this.updateColors();
    this.resizeCanvas();

    window.addEventListener('resize', () => this.resizeCanvas());

    this.observeDarkMode();
  }

  updateColors() {
    if (this.isDark) {
      this.colors = [
        'rgba(211, 188, 142, 0.6)',
        'rgba(255, 255, 255, 0.5)',
        'rgba(66, 133, 244, 0.4)',
        'rgba(252, 211, 77, 0.4)'
      ];
    } else {
      this.colors = [
        'rgba(180, 83, 9, 0.3)',
        'rgba(59, 130, 246, 0.3)',
        'rgba(148, 163, 184, 0.4)'
      ];
    }
  }

  observeDarkMode() {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.attributeName === 'class') {
          const newIsDark = document.documentElement.classList.contains('dark');
          if (newIsDark !== this.isDark) {
            this.isDark = newIsDark;
            this.updateColors();
            this.particles.forEach(p => {
              p.color = this.colors[Math.floor(Math.random() * this.colors.length)];
            });
          }
        }
      });
    });
    
    observer.observe(document.documentElement, { attributes: true });
  }

  resizeCanvas() {
    this.canvas.width = window.innerWidth;
    this.canvas.height = window.innerHeight;
    this.createParticles();
  }

  createParticles() {
    this.particles = [];
    
    for (let i = 0; i < this.config.particleCount; i++) {
      this.particles.push(this.createParticle(true));
    }
  }

  createParticle(initial = false) {
    return {
      x: Math.random() * this.canvas.width,
      y: initial ? Math.random() * this.canvas.height : this.canvas.height + 20,
      size: Math.random() * 5 + 3,
      color: this.colors[Math.floor(Math.random() * this.colors.length)],
      speedY: Math.random() * -0.5 - 0.1,
      speedX: (Math.random() - 0.5) * 0.2,
      opacity: 0,
      fadeSpeed: Math.random() * 0.01 + 0.005,
      type: Math.random() > 0.7 ? 'star' : 'circle',
      rotation: Math.random() * Math.PI * 2,
      rotationSpeed: (Math.random() - 0.5) * 0.02
    };
  }

  resetParticle(particle) {
    particle.x = Math.random() * this.canvas.width;
    particle.y = this.canvas.height + 20;
    particle.size = Math.random() * 5 + 3;
    particle.color = this.colors[Math.floor(Math.random() * this.colors.length)];
    particle.speedY = Math.random() * -0.5 - 0.1;
    particle.speedX = (Math.random() - 0.5) * 0.2;
    particle.opacity = 0;
    particle.fadeSpeed = Math.random() * 0.01 + 0.005;
    particle.type = Math.random() > 0.7 ? 'star' : 'circle';
    particle.rotation = Math.random() * Math.PI * 2;
    particle.rotationSpeed = (Math.random() - 0.5) * 0.02;
  }

  drawStar(ctx, x, y, size, rotation) {
    ctx.save();
    ctx.translate(x, y);
    ctx.rotate(rotation);
    
    const r = size * 2;
    ctx.beginPath();
    ctx.moveTo(0, -r);
    ctx.quadraticCurveTo(0.5, -0.5, r, 0);
    ctx.quadraticCurveTo(0.5, 0.5, 0, r);
    ctx.quadraticCurveTo(-0.5, 0.5, -r, 0);
    ctx.quadraticCurveTo(-0.5, -0.5, 0, -r);
    ctx.fill();
    
    ctx.restore();
  }

  drawCircle(ctx, x, y, size) {
    ctx.beginPath();
    ctx.arc(x, y, size, 0, Math.PI * 2);
    ctx.fill();
  }

  update() {
    this.particles.forEach(particle => {
      particle.y += particle.speedY;
      particle.x += particle.speedX;
      particle.rotation += particle.rotationSpeed;

      if (particle.y < this.canvas.height * 0.2) {
        particle.opacity -= particle.fadeSpeed;
      } else if (particle.opacity < 1) {
        particle.opacity += particle.fadeSpeed;
      }

      if (particle.y < -20 || particle.opacity <= 0) {
        this.resetParticle(particle);
      }

      if (!this.ctx) return;

      this.ctx.save();
      this.ctx.globalAlpha = particle.opacity;
      this.ctx.fillStyle = particle.color;

      if (particle.type === 'star') {
        this.drawStar(this.ctx, particle.x, particle.y, particle.size, particle.rotation);
      } else {
        this.drawCircle(this.ctx, particle.x, particle.y, particle.size);
      }

      this.ctx.restore();
    });
  }

  start() {
    if (this.isActive) return;
    
    this.isActive = true;
    this.animate();
  }

  stop() {
    if (!this.isActive) return;
    
    this.isActive = false;
    if (this.animationId) {
      cancelAnimationFrame(this.animationId);
      this.animationId = null;
    }
  }

  toggle() {
    if (this.isActive) {
      this.stop();
      this.canvas.style.display = 'none';
    } else {
      this.start();
      this.canvas.style.display = 'block';
    }
    localStorage.setItem('clarity_particles_enabled', this.isActive ? '1' : '0');
  }

  animate() {
    if (!this.isActive || !this.ctx) return;

    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    
    this.update();
    
    this.animationId = requestAnimationFrame(() => this.animate());
  }
}

let particlesInstance = null;

function initParticles(config) {
  if (!particlesInstance) {
    window.particlesConfig = config;
    particlesInstance = new ParticlesBackground();
    
    particlesInstance.start();
    
    const savedState = localStorage.getItem('clarity_particles_enabled');
    if (savedState === '0') {
      particlesInstance.stop();
      const canvas = document.getElementById('particles-canvas');
      if (canvas) {
        canvas.style.display = 'none';
      }
    }
  }
  return particlesInstance;
}

function toggleParticles() {
  if (particlesInstance) {
    particlesInstance.toggle();
    return particlesInstance.isActive;
  }
  return false;
}

function getParticlesState() {
  return particlesInstance ? particlesInstance.isActive : false;
}

if (typeof window !== 'undefined') {
  window.initParticles = initParticles;
  window.toggleParticles = toggleParticles;
  window.getParticlesState = getParticlesState;
}
