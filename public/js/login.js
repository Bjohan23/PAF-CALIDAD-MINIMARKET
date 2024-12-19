document.addEventListener("DOMContentLoaded", function () {
  // Función para cerrar el mensaje de error
  document.querySelectorAll(".close-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      this.parentElement.style.display = "none";
    });
  });

  // Función para ocultar el mensaje de error después de 5 segundos
  const errorMessage = document.querySelector(".error-message");
  if (errorMessage) {
    const progressBar = errorMessage.querySelector(".progress-bar");
    if (progressBar) {
      progressBar.style.transition = "width 5s linear";
      progressBar.style.width = "100%";
    }

    setTimeout(() => {
      errorMessage.style.display = "none";
    }, 5000);
  }
});
