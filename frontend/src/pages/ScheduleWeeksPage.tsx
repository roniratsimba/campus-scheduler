import { useEffect, useState } from "react";
import { api } from "../service/api";
import Modal from "../components/Modal";

type ScheduleWeek = {
  id: number;
  startDate: string;
  endDate: string;
  status: string;
  publishedAt: string | null;
};

type ScheduleWeekFormData = {
  startDate: string;
  endDate: string;
};

export default function ScheduleWeeksPage() {
  const [weeks, setWeeks] = useState<ScheduleWeek[]>([]);
  const [loading, setLoading] = useState(true);
  const [copySourceId, setCopySourceId] = useState<number | null>(null);
  const [copyTargetId, setCopyTargetId] = useState<number | null>(null);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingWeek, setEditingWeek] = useState<ScheduleWeek | null>(null);
  const [formData, setFormData] = useState<ScheduleWeekFormData>({
    startDate: '',
    endDate: ''
  });

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

  const handleDelete = (id: number) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer cette semaine ?')) {
      api.delete(`/schedule-weeks/${id}`).then(() => {
        setWeeks(weeks.filter(w => w.id !== id));
      });
    }
  };

  const handleAdd = () => {
    setEditingWeek(null);
    setFormData({ startDate: '', endDate: '' });
    setIsModalOpen(true);
  };

  const handleEdit = (week: ScheduleWeek) => {
    setEditingWeek(week);
    setFormData({
      startDate: week.startDate,
      endDate: week.endDate
    });
    setIsModalOpen(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      if (editingWeek) {
        const response = await api.put(`/schedule-weeks/${editingWeek.id}`, formData);
        setWeeks(weeks.map(w => w.id === editingWeek.id ? response.data : w));
      } else {
        const response = await api.post('/schedule-weeks', formData);
        setWeeks([...weeks, response.data]);
      }
      setIsModalOpen(false);
    } catch (error) {
      console.error('Erreur lors de la sauvegarde', error);
      alert('Erreur lors de la sauvegarde');
    }
  };

  if (loading) {
    return <div className="loading">Chargement...</div>;
  }

  return (
    <div className="mesh-gradient" style={{ position: "relative", zIndex: 1 }}>
      <div className="flex-between" style={{ marginBottom: "2rem" }}>
        <h1>Semaines d'emploi du temps</h1>
        <button className="btn-accent" style={{ padding: "0.625rem 1.25rem" }} onClick={handleAdd}>
          + Ajouter
        </button>
      </div>

      <div className="card card-gradient" style={{ border: "1px solid rgba(99, 102, 241, 0.1)", padding: "1.5rem", marginBottom: "2rem" }}>
        <h3 style={{ marginBottom: "1rem", fontSize: "1rem", fontWeight: 500 }}>Copier une semaine</h3>
        <div className="flex" style={{ gap: "1rem" }}>
          <select
            value={copySourceId || ""}
            onChange={(e) => setCopySourceId(Number(e.target.value))}
            style={{ flex: 1 }}
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
            style={{ flex: 1 }}
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
            className="btn-primary"
            style={{ padding: "0.625rem 1.25rem" }}
          >
            Copier
          </button>
        </div>
      </div>

      <div className="card card-gradient" style={{ border: "1px solid rgba(99, 102, 241, 0.1)", padding: "0" }}>
        <table style={{ border: "none", borderRadius: 0 }}>
          <thead>
            <tr>
              <th>ID</th>
              <th>Début</th>
              <th>Fin</th>
              <th>Statut</th>
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
                <td>
                  <span className={`badge ${week.status === 'PUBLISHED' ? 'badge-success' : 'badge-warning'}`}>
                    {week.status}
                  </span>
                </td>
                <td>{week.publishedAt ?? "-"}</td>
                <td>
                  <div className="flex" style={{ gap: "0.5rem" }}>
                    {week.status !== 'PUBLISHED' && (
                      <button
                        onClick={() => handlePublish(week.id)}
                        className="btn-secondary"
                        style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem" }}
                      >
                        Publier
                      </button>
                    )}
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem" }}
                      onClick={() => handleEdit(week)}
                    >
                      Modifier
                    </button>
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem", color: "var(--danger)", borderColor: "var(--danger)" }}
                      onClick={() => handleDelete(week.id)}
                    >
                      Supprimer
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title={editingWeek ? 'Modifier la semaine' : 'Ajouter une semaine'}
      >
        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: "1rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Date de début *
            </label>
            <input
              type="date"
              value={formData.startDate}
              onChange={(e) => setFormData({ ...formData, startDate: e.target.value })}
              required
              style={{ width: "100%" }}
            />
          </div>
          <div style={{ marginBottom: "1.5rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Date de fin *
            </label>
            <input
              type="date"
              value={formData.endDate}
              onChange={(e) => setFormData({ ...formData, endDate: e.target.value })}
              required
              style={{ width: "100%" }}
            />
          </div>
          <div className="flex" style={{ gap: "0.75rem", justifyContent: "flex-end" }}>
            <button
              type="button"
              className="btn-secondary"
              onClick={() => setIsModalOpen(false)}
            >
              Annuler
            </button>
            <button type="submit" className="btn-primary">
              {editingWeek ? 'Modifier' : 'Ajouter'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}