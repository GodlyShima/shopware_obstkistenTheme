
// offcanvas-fix.js
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node.nodeType === 1 && node.classList.contains('cart-offcanvas')) {
          const hasHeader = node.querySelector('.offcanvas-header');
          const body = node.querySelector('.offcanvas-body');
  
          if (!hasHeader && body) {
            body.style.borderRadius = '8px';
          }
        }
      });
    });
  });
  
  // Startet die Überwachung auf dem body
  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
  

console.log('ObstkistenTheme loaded | made by Len Knutzen');


  