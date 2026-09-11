import axios from "axios";

const baseURL =
  (import.meta.env.VITE_API_URL as string | undefined) ??
  "http://127.0.0.1:8000/api";

export const api = axios.create({
  baseURL,
});

// Attache le jeton d'authentification (placeholder JWT) à chaque requête
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});