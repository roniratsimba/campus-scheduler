import { useEffect, useState } from "react";
import { api } from "../service/api";
import Modal from "../components/Modal";

type Subject = {
  id: number;
  code: string;
  name: string;
};

type SubjectFormData = {
  code: string;
  name: string;
};

export default function SubjectsPage() {
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingSubject, setEditingSubject] = useState<Subject | null>(null);
  const [formData, setFormData] = useState<SubjectFormData>({
    code: '',
    name: ''
  });

  useEffect(() => {
    api
      .get("/subjects")
      .then((response) => {
        setSubjects(response.data);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  const handleDelete = (id: number) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')) {
      api.delete(`/subjects/${id}`).then(() => {
        setSubjects(subjects.filter(s => s.id !== id));
      });
    }
  };

  const handleAdd = () => {
    setEditingSubject(null);
    setFormData({ code: '', name: '' });
    setIsModalOpen(true);
  };

  const handleEdit = (subject: Subject) => {
    setEditingSubject(subject);
    setFormData({
      code: subject.code,
      name: subject.name
    });
    setIsModalOpen(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      if (editingSubject) {
        const response = await api.put(`/subjects/${editingSubject.id}`, formData);
        setSubjects(subjects.map(s => s.id === editingSubject.id ? response.data : s));
      } else {
        const response = await api.post('/subjects', formData);
        setSubjects([...subjects, response.data]);
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
        <h1>Matières</h1>
        <button className="btn-accent" style={{ padding: "0.625rem 1.25rem" }} onClick={handleAdd}>
          + Ajouter
        </button>
      </div>

      <div className="card card-gradient" style={{ border: "1px solid rgba(16, 185, 129, 0.1)", padding: "0" }}>
        <table style={{ border: "none", borderRadius: 0 }}>
          <thead>
            <tr>
              <th>ID</th>
              <th>Code</th>
              <th>Nom</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            {subjects.map((subject) => (
              <tr key={subject.id}>
                <td>{subject.id}</td>
                <td>
                  <span className="badge badge-accent">{subject.code}</span>
                </td>
                <td>{subject.name}</td>
                <td>
                  <div className="flex" style={{ gap: "0.5rem" }}>
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem" }}
                      onClick={() => handleEdit(subject)}
                    >
                      Modifier
                    </button>
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem", color: "var(--danger)", borderColor: "var(--danger)" }}
                      onClick={() => handleDelete(subject.id)}
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
        title={editingSubject ? 'Modifier la matière' : 'Ajouter une matière'}
      >
        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: "1rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Code *
            </label>
            <input
              type="text"
              value={formData.code}
              onChange={(e) => setFormData({ ...formData, code: e.target.value })}
              required
              style={{ width: "100%" }}
            />
          </div>
          <div style={{ marginBottom: "1.5rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Nom *
            </label>
            <input
              type="text"
              value={formData.name}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
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
              {editingSubject ? 'Modifier' : 'Ajouter'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}