import { Controller } from "@hotwired/stimulus";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
  connect() {
    //this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js';
  }
  static targets = ["body"];
  //darkMode = false;

  connect() {
    this.darkMode = JSON.parse(localStorage.getItem("darkMode")) ?? false;
    this.updateTheme();
  }

  toggleDarkMode(event) {
    this.darkMode = !this.darkMode;
    localStorage.setItem("darkMode", this.darkMode);
    this.updateTheme();
  }

  updateTheme() {
    this.bodyTarget.setAttribute("data-bs-theme", this.darkMode ? "dark" : "");
  }
}
