import { useEffect, useState } from "react";
import { api } from "../service/api";
import Modal from "../components/Modal";

type Teacher = {
  id: number;
  firstName: string;
  lastName: string | null;
  email: string;
  active: boolean;
};

type TeacherFormData = {
  firstName: string;
  lastName: string;
  email: string;
  active: boolean;
};

export default function TeachersPage() {
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingTeacher, setEditingTeacher] = useState<Teacher | null>(null);
  const [formData, setFormData] = useState<TeacherFormData>({
    firstName: '',
    lastName: '',
    email: '',
    active: true
  });

  useEffect(() => {
    api
      .get("/teachers")
      .then((response) => {
        setTeachers(response.data);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  const handleDelete = (id: number) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer cet enseignant ?')) {
      api.delete(`/teachers/${id}`).then(() => {
        setTeachers(teachers.filter(t => t.id !== id));
      });
    }
  };

  const handleAdd = () => {
    setEditingTeacher(null);
    setFormData({ firstName: '', lastName: '', email: '', active: true });
    setIsModalOpen(true);
  };

  const handleEdit = (teacher: Teacher) => {
    setEditingTeacher(teacher);
    setFormData({
      firstName: teacher.firstName,
      lastName: teacher.lastName || '',
      email: teacher.email,
      active: teacher.active
    });
    setIsModalOpen(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      if (editingTeacher) {
        const response = await api.put(`/teachers/${editingTeacher.id}`, formData);
        setTeachers(teachers.map(t => t.id === editingTeacher.id ? response.data : t));
      } else {
        const response = await api.post('/teachers', formData);
        setTeachers([...teachers, response.data]);
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
        <h1>Enseignants</h1>
        <button className="btn-accent" style={{ padding: "0.625rem 1.25rem" }} onClick={handleAdd}>
          + Ajouter
        </button>
      </div>

      <div className="card card-gradient" style={{ border: "1px solid rgba(99, 102, 241, 0.1)", padding: "0" }}>
        <table style={{ border: "none", borderRadius: 0 }}>
          <thead>
            <tr>
              <th>ID</th>
              <th>Prénom</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            {teachers.map((teacher) => (
              <tr key={teacher.id}>
                <td>{teacher.id}</td>
                <td>{teacher.firstName}</td>
                <td>{teacher.lastName || '-'}</td>
                <td>{teacher.email}</td>
                <td>
                  <span className={`badge ${teacher.active ? 'badge-success' : 'badge-warning'}`}>
                    {teacher.active ? 'Actif' : 'Inactif'}
                  </span>
                </td>
                <td>
                  <div className="flex" style={{ gap: "0.5rem" }}>
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem" }}
                      onClick={() => handleEdit(teacher)}
                    >
                      Modifier
                    </button>
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem", color: "var(--danger)", borderColor: "var(--danger)" }}
                      onClick={() => handleDelete(teacher.id)}
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
        title={editingTeacher ? 'Modifier l\'enseignant' : 'Ajouter un enseignant'}
      >
        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: "1rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Prénom *
            </label>
            <input
              type="text"
              value={formData.firstName}
              onChange={(e) => setFormData({ ...formData, firstName: e.target.value })}
              required
              style={{ width: "100%" }}
            />
          </div>
          <div style={{ marginBottom: "1rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Nom
            </label>
            <input
              type="text"
              value={formData.lastName}
              onChange={(e) => setFormData({ ...formData, lastName: e.target.value })}
              style={{ width: "100%" }}
            />
          </div>
          <div style={{ marginBottom: "1rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Email *
            </label>
            <input
              type="email"
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
              required
              style={{ width: "100%" }}
            />
          </div>
          <div style={{ marginBottom: "1.5rem" }}>
            <label style={{ display: "flex", alignItems: "center", gap: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              <input
                type="checkbox"
                checked={formData.active}
                onChange={(e) => setFormData({ ...formData, active: e.target.checked })}
              />
              Actif
            </label>
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
              {editingTeacher ? 'Modifier' : 'Ajouter'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}