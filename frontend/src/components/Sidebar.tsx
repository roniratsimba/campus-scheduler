import { Link, useLocation } from "react-router-dom";

const menuItems = [
  { text: "Dashboard", icon: "📊", path: "/dashboard" },
  { text: "Enseignants", icon: "👨‍🏫", path: "/teachers" },
  { text: "Matières", icon: "📚", path: "/subjects" },
  { text: "Salles", icon: "🏢", path: "/rooms" },
  { text: "Groupes", icon: "👥", path: "/academic-groups" },
  { text: "Semaines", icon: "📅", path: "/schedule-weeks" },
  { text: "Emploi du temps", icon: "📋", path: "/course-sessions" },
  { text: "Salles libres", icon: "🔍", path: "/free-rooms" },
];

export default function Sidebar() {
  const location = useLocation();

  return (
    <aside style={{
      width: "280px",
      background: "var(--primary)",
      color: "white",
      height: "100vh",
      position: "fixed",
      left: 0,
      top: 0,
      display: "flex",
      flexDirection: "column",
      padding: "1.5rem",
    }}>
      <div style={{ marginBottom: "2rem" }}>
        <h2 style={{ color: "white", margin: "0 0 0.5rem 0" }}>
          Campus Scheduler
        </h2>
        <p style={{ color: "rgba(255,255,255,0.7)", margin: 0, fontSize: "0.875rem" }}>
          Administration
        </p>
      </div>

      <nav style={{ flex: 1 }}>
        <ul style={{ listStyle: "none", padding: 0, margin: 0 }}>
          {menuItems.map((item) => {
            const isActive = location.pathname === item.path;
            return (
              <li key={item.text} style={{ marginBottom: "0.5rem" }}>
                <Link
                  to={item.path}
                  style={{
                    display: "flex",
                    alignItems: "center",
                    padding: "0.75rem 1rem",
                    borderRadius: "8px",
                    textDecoration: "none",
                    color: "white",
                    background: isActive ? "rgba(255,255,255,0.15)" : "transparent",
                    transition: "background 0.2s",
                  }}
                  onMouseEnter={(e) => {
                    if (!isActive) e.currentTarget.style.background = "rgba(255,255,255,0.1)";
                  }}
                  onMouseLeave={(e) => {
                    if (!isActive) e.currentTarget.style.background = "transparent";
                  }}
                >
                  <span style={{ marginRight: "0.75rem", fontSize: "1.25rem" }}>
                    {item.icon}
                  </span>
                  <span style={{ fontWeight: isActive ? 600 : 400 }}>
                    {item.text}
                  </span>
                </Link>
              </li>
            );
          })}
        </ul>
      </nav>

      <Link
        to="/"
        style={{
          display: "flex",
          alignItems: "center",
          padding: "0.75rem 1rem",
          borderRadius: "8px",
          textDecoration: "none",
          color: "white",
          background: "rgba(255,255,255,0.1)",
          transition: "background 0.2s",
        }}
      >
        <span style={{ marginRight: "0.75rem", fontSize: "1.25rem" }}>
          🏠
        </span>
        <span>Retour accueil</span>
      </Link>
    </aside>
  );
}