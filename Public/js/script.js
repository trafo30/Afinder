document.addEventListener("DOMContentLoaded", () => {
  console.log('✔ script.js cargado');
    const modal    = document.getElementById("loginModal");
  const loginBtn = document.querySelector(".login-button");
  const closeBtn = modal.querySelector(".close");

  loginBtn.addEventListener("click", () => {
    modal.classList.add("open");
  });

  closeBtn.addEventListener("click", () => {
    modal.classList.remove("open");
  });

  window.addEventListener("click", e => {
    if (e.target === modal) {
      modal.classList.remove("open");
    }
  });
});