import { useEffect, useState } from "react";
import { api } from "../service/api";

type ScheduleWeek = {
  id: number;
  startDate: string;
  endDate: string;
  status: string;
  publishedAt: string | null;
};

export default function ScheduleWeeksPage() {
  const [weeks, setWeeks] = useState<ScheduleWeek[]>([]);
  const [loading, setLoading] = useState(true);
  const [copySourceId, setCopySourceId] = useState<number | null>(null);
  const [copyTargetId, setCopyTargetId] = useState<number | null>(null);

  useEffect(() => {
    api
      .get("/schedule-weeks")
      .then((response) => {
        setWeeks(response.data);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  const handlePublish = async (id: number) => {
    try {
      await api.post(`/schedule-weeks/${id}/publish`);
      setWeeks(weeks.map(w => 
        w.id === id 
          ? { ...w, status: 'PUBLISHED', publishedAt: new Date().toISOString() }
          : w
      ));
    } catch (error) {
      console.error("Erreur lors de la publication", error);
    }
  };

  const handleCopy = async () => {
    if (!copySourceId || !copyTargetId) return;

    try {
      await api.post(`/schedule-weeks/${copySourceId}/copy`, { targetWeekId: copyTargetId });
      alert("Semaine copiée avec succès");
      setCopySourceId(null);
      setCopyTargetId(null);
    } catch (error) {
      console.error("Erreur lors de la copie", error);
      alert("Erreur lors de la copie");
    }
  };

  if (loading) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>Semaines d'emploi du temps</h1>

      <div style={{ marginBottom: "2rem", padding: "1rem", background: "#f5f5f5" }}>
        <h3>Copier une semaine</h3>
        <select
          value={copySourceId || ""}
          onChange={(e) => setCopySourceId(Number(e.target.value))}
          style={{ marginRight: "1rem", padding: "0.5rem" }}
        >
          <option value="">Semaine source...</option>
          {weeks.map((week) => (
            <option key={week.id} value={week.id}>
              {week.startDate} - {week.endDate} ({week.status})
            </option>
          ))}
        </select>

        <select
          value={copyTargetId || ""}
          onChange={(e) => setCopyTargetId(Number(e.target.value))}
          style={{ marginRight: "1rem", padding: "0.5rem" }}
        >
          <option value="">Semaine cible...</option>
          {weeks.map((week) => (
            <option key={week.id} value={week.id}>
              {week.startDate} - {week.endDate} ({week.status})
            </option>
          ))}
        </select>

        <button
          onClick={handleCopy}
          disabled={!copySourceId || !copyTargetId}
          style={{ padding: "0.5rem 1rem" }}
        >
          Copier
        </button>
      </div>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Status</th>
            <th>Publié</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          {weeks.map((week) => (
            <tr key={week.id}>
              <td>{week.id}</td>
              <td>{week.startDate}</td>
              <td>{week.endDate}</td>
              <td>{week.status}</td>
              <td>{week.publishedAt ?? "-"}</td>
              <td>
                {week.status !== 'PUBLISHED' && (
                  <button
                    onClick={() => handlePublish(week.id)}
                    style={{ padding: "0.25rem 0.5rem" }}
                  >
                    Publier
                  </button>
                )}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}