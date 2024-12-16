(function() {
  const scale = 100 / 750.0; //设计稿大小
  const html = document.getElementsByTagName('html')[0];
  const resize = function() {
    var size = scale * document.documentElement.clientWidth;
    html.style.fontSize = (size > 70 ? 70 : size) + "px";
  };
  const resizeEvt = window.orientationchange ? 'orientationchange' : 'resize';

  document.addEventListener("DOMContentLoaded", resize, false);
  window.addEventListener(resizeEvt, resize, false);
})();
