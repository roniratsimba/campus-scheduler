import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { api } from "../service/api";
import { Card } from "../components/ui/Card";
import { Button } from "../components/ui/Button";
import { Input } from "../components/ui/Input";
import { toast } from "../components/ui/ToastContainer";
import { Lock, Mail, ArrowLeft } from "lucide-react";

export default function LoginPage() {
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    try {
      const response = await api.post("/login", { email, password });
      localStorage.setItem("token", response.data.token);
      localStorage.setItem("user", JSON.stringify(response.data.user));
      toast.success("Connexion réussie !");
      navigate("/dashboard");
    } catch {
      setError("Identifiants incorrects");
      toast.error("Identifiants incorrects");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <Card className="p-8">
          <div className="text-center mb-8">
            <h1 className="text-3xl font-light text-night-950 mb-2">Connexion</h1>
            <p className="text-gray-600">Accédez à l'administration Campus Scheduler</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            <Input
              type="email"
              label="Email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              autoFocus
              placeholder="votre@email.com"
              error={error ? "" : undefined}
              icon={<Mail className="w-5 h-5 text-gray-400" />}
            />

            <Input
              type="password"
              label="Mot de passe"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              placeholder="••••••••"
              error={error ? "" : undefined}
              icon={<Lock className="w-5 h-5 text-gray-400" />}
            />

            {error && (
              <div className="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                {error}
              </div>
            )}

            <Button
              type="submit"
              variant="primary"
              loading={loading}
              fullWidth
              className="w-full"
            >
              {loading ? "Connexion..." : "Se connecter"}
            </Button>
          </form>

          <div className="mt-6 pt-6 border-t border-gray-200">
            <Button
              variant="ghost"
              onClick={() => navigate("/")}
              fullWidth
              className="w-full"
            >
              <ArrowLeft className="w-4 h-4 mr-2" />
              Retour accueil
            </Button>
          </div>
        </Card>
      </div>
    </div>
  );
}
