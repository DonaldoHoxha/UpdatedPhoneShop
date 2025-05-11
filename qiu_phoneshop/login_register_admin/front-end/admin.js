const registerBtn = document.getElementById("register");
const container = document.getElementById("container");
const loginBtn = document.getElementById("login");

//aggiungere una classe al container quando si fa clic sul pulsante di registrazione
registerBtn.addEventListener("click", () => {
    container.classList.add("active");
}
);

//tolgo la classe al container quando si fa clic sul pulsante di login
loginBtn.addEventListener("click", () => {
    container.classList.remove("active");
}
);