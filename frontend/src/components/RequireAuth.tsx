import { Navigate } from "react-router-dom";

/**
 * Garde d'authentification pour les routes d'administration.
 * Redirige vers /login si aucun jeton n'est présent.
 */
export default function RequireAuth({ children }: { children: React.ReactNode }) {
  const token = localStorage.getItem("token");

  if (!token) {
    return <Navigate to="/login" replace />;
  }

  return <>{children}</>;
}