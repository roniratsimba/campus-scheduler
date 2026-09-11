import { useEffect, useState } from "react";
import { api } from "../service/api";
import Modal from "../components/Modal";

type AcademicGroup = {
  id: number;
  groupNumber: number;
  level: string;
  program: string;
};

type AcademicGroupFormData = {
  groupNumber: number;
  level: string;
  program: string;
};

export default function AcademicGroupsPage() {
  const [groups, setGroups] = useState<AcademicGroup[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingGroup, setEditingGroup] = useState<AcademicGroup | null>(null);
  const [formData, setFormData] = useState<AcademicGroupFormData>({
    groupNumber: 1,
    level: 'L1',
    program: 'GB'
  });

  useEffect(() => {
    api
      .get("/academic-groups")
      .then((response) => {
        setGroups(response.data);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  const handleDelete = (id: number) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?')) {
      api.delete(`/academic-groups/${id}`).then(() => {
        setGroups(groups.filter(g => g.id !== id));
      });
    }
  };

  const handleAdd = () => {
    setEditingGroup(null);
    setFormData({ groupNumber: 1, level: 'L1', program: 'GB' });
    setIsModalOpen(true);
  };

  const handleEdit = (group: AcademicGroup) => {
    setEditingGroup(group);
    setFormData({
      groupNumber: group.groupNumber,
      level: group.level,
      program: group.program
    });
    setIsModalOpen(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      if (editingGroup) {
        const response = await api.put(`/academic-groups/${editingGroup.id}`, formData);
        setGroups(groups.map(g => g.id === editingGroup.id ? response.data : g));
      } else {
        const response = await api.post('/academic-groups', formData);
        setGroups([...groups, response.data]);
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
        <h1>Groupes académiques</h1>
        <button className="btn-accent" style={{ padding: "0.625rem 1.25rem" }} onClick={handleAdd}>
          + Ajouter
        </button>
      </div>

      <div className="card card-gradient" style={{ border: "1px solid rgba(99, 102, 241, 0.1)", padding: "0" }}>
        <table style={{ border: "none", borderRadius: 0 }}>
          <thead>
            <tr>
              <th>ID</th>
              <th>Niveau</th>
              <th>Programme</th>
              <th>Groupe</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            {groups.map((group) => (
              <tr key={group.id}>
                <td>{group.id}</td>
                <td>
                  <span className="badge badge-accent">{group.level}</span>
                </td>
                <td>{group.program}</td>
                <td>
                  <span className="badge badge-success">G{group.groupNumber}</span>
                </td>
                <td>
                  <div className="flex" style={{ gap: "0.5rem" }}>
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem" }}
                      onClick={() => handleEdit(group)}
                    >
                      Modifier
                    </button>
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem", color: "var(--danger)", borderColor: "var(--danger)" }}
                      onClick={() => handleDelete(group.id)}
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
        title={editingGroup ? 'Modifier le groupe' : 'Ajouter un groupe'}
      >
        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: "1rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Niveau *
            </label>
            <select
              value={formData.level}
              onChange={(e) => setFormData({ ...formData, level: e.target.value })}
              required
              style={{ width: "100%" }}
            >
              <option value="L1">L1</option>
              <option value="L2">L2</option>
              <option value="L3">L3</option>
              <option value="M1">M1</option>
              <option value="M2">M2</option>
            </select>
          </div>
          <div style={{ marginBottom: "1rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Programme *
            </label>
            <select
              value={formData.program}
              onChange={(e) => setFormData({ ...formData, program: e.target.value })}
              required
              style={{ width: "100%" }}
            >
              <option value="GB">Génie Logiciel</option>
              <option value="SR">Systèmes et Réseaux</option>
              <option value="ASI">Administration Systèmes</option>
              <option value="IA">Intelligence Artificielle</option>
              <option value="DS">Data Science</option>
            </select>
          </div>
          <div style={{ marginBottom: "1.5rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Numéro de groupe *
            </label>
            <input
              type="number"
              value={formData.groupNumber}
              onChange={(e) => setFormData({ ...formData, groupNumber: parseInt(e.target.value) })}
              required
              min="1"
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
              {editingGroup ? 'Modifier' : 'Ajouter'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}