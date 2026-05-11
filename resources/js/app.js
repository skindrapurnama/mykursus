import "./bootstrap";
import Alpine from "alpinejs";
import mask from "@alpinejs/mask"; // 1. Import plugin

Alpine.plugin(mask); // 2. Daftarkan plugin
window.Alpine = Alpine;
Alpine.start();
