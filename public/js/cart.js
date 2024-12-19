function addToCart(productId) {
  const cantidad = document.getElementById("cantidad")?.value || 1;
  const data = {
    producto_id: productId,
    cantidad: parseInt(cantidad),
  };

  console.log("Enviando datos:", data);

  fetch(`${BASE_URL}carrito/agregar`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify(data),
  })
    .then(async (response) => {
      console.log("Response status:", response.status);
      const text = await response.text(); // Primero obtenemos el texto de la respuesta
      console.log("Response text:", text); // Lo mostramos para debug

      try {
        return JSON.parse(text); // Intentamos parsearlo como JSON
      } catch (e) {
        console.error("Error parsing JSON:", e);
        throw new Error("Invalid JSON response");
      }
    })
    .then((data) => {
      console.log("Response data:", data);
      if (data.success) {
        updateCartCounter(data.cart_count);
        showAlert("success", data.message);

        if (data.redirect) {
          window.location.href = data.redirect;
        }
      } else {
        showAlert("error", data.message);
        if (data.redirect) {
          window.location.href = data.redirect;
        }
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showAlert("error", "Error al agregar al carrito: " + error.message);
    });
}

// Función para actualizar el contador del carrito
function updateCartCounter(count) {
  const cartCount = document.querySelector(".cart-count");
  if (cartCount) {
    cartCount.textContent = count;
    cartCount.classList.add("cart-pulse");
    setTimeout(() => {
      cartCount.classList.remove("cart-pulse");
    }, 1000);
  }
}

// Función para mostrar alertas
function showAlert(type, message) {
  const alertElement = document.createElement("div");
  alertElement.className = `fixed top-4 right-4 z-50 rounded-lg border-l-4 p-4 ${
    type === "success"
      ? "bg-green-100 border-green-500 text-green-700"
      : "bg-red-100 border-red-500 text-red-700"
  }`;

  alertElement.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
              type === "success" ? "fa-check-circle text-green-500" : "fa-times-circle text-red-500"
            } mr-2 text-xl"></i>
            <p>${message}</p>
        </div>
    `;

  document.body.appendChild(alertElement);

  setTimeout(() => {
    alertElement.remove();
  }, 5000);
}

// Funciones específicas para la página de producto
const productPage = {
  decrementQuantity() {
    const input = document.getElementById("cantidad");
    const newValue = parseInt(input.value) - 1;
    if (newValue >= parseInt(input.min)) {
      input.value = newValue;
    }
  },

  incrementQuantity() {
    const input = document.getElementById("cantidad");
    const newValue = parseInt(input.value) + 1;
    if (newValue <= parseInt(input.max)) {
      input.value = newValue;
    }
  },

  cambiarImagen(url) {
    document.getElementById("main-image").src = url;
  },
};
