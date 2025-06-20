import {
  Alert,
  Button,
  Carousel,
  Collapse,
  Dropdown,
  Modal,
  Offcanvas,
  Popover,
  ScrollSpy,
  Tab,
  Toast,
  Tooltip,
  bootstrap,
  tabler,
} from "@tabler/core";
import axios from "axios";

declare global {
  interface Window {
    // app bootstrap
    axios: typeof axios;

    // tabler
    Alert: typeof Alert;
    Button: typeof Button;
    Carousel: typeof Carousel;
    Collapse: typeof Collapse;
    Dropdown: typeof Dropdown;
    Modal: typeof Modal;
    Offcanvas: typeof Offcanvas;
    Popover: typeof Popover;
    ScrollSpy: typeof ScrollSpy;
    Tab: typeof Tab;
    Toast: typeof Toast;
    Tooltip: typeof Tooltip;
    bootstrap: typeof bootstrap;
    tabler: typeof tabler;
  }
}
