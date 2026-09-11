import { useEffect, useState } from "react";
import { api } from "../service/api";
import Modal from "../components/Modal";

type Room = {
  id: number;
  code: string;
  name: string;
  type: string;
};

type RoomFormData = {
  code: string;
  name: string;
  type: string;
};

export default function RoomsPage() {
  const [rooms, setRooms] = useState<Room[]>([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingRoom, setEditingRoom] = useState<Room | null>(null);
  const [formData, setFormData] = useState<RoomFormData>({
    code: '',
    name: '',
    type: 'CLASSROOM'
  });

  useEffect(() => {   
    api
      .get("/rooms")
      .then((response) => {   
        setRooms(response.data);
      })
      .finally(() => {   
        setLoading(false);
      });
  }, []);

  const handleAdd = () => {
    setEditingRoom(null);
    setFormData({ code: '', name: '', type: 'CLASSROOM' });
    setIsModalOpen(true);
  };

  const handleEdit = (room: Room) => {
    setEditingRoom(room);
    setFormData({
      code: room.code,
      name: room.name,
      type: room.type
    });
    setIsModalOpen(true);
  };

  const handleDelete = (id: number) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer cette salle ?')) {
      api.delete(`/rooms/${id}`).then(() => {
        setRooms(rooms.filter(r => r.id !== id));
      });
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      if (editingRoom) {
        const response = await api.put(`/rooms/${editingRoom.id}`, formData);
        setRooms(rooms.map(r => r.id === editingRoom.id ? response.data : r));
      } else {
        const response = await api.post('/rooms', formData);
        setRooms([...rooms, response.data]);
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
        <h1>Salles</h1>
        <button className="btn-accent" style={{ padding: "0.625rem 1.25rem" }} onClick={handleAdd}>
          + Ajouter
        </button>
      </div>

      <div className="card card-gradient" style={{ border: "1px solid rgba(245, 158, 11, 0.1)", padding: "0" }}>
        <table style={{ border: "none", borderRadius: 0 }}>
          <thead>
            <tr>
              <th>ID</th>
              <th>Code</th>
              <th>Nom</th>
              <th>Type</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {rooms.map((room) => (
              <tr key={room.id}>
                <td>{room.id}</td>
                <td>
                  <span className="badge badge-warning">{room.code}</span>
                </td>
                <td>{room.name}</td>
                <td>
                  <span className={`badge ${room.type === 'CLASSROOM' ? 'badge-success' : room.type === 'LABORATORY' ? 'badge-accent' : 'badge-warning'}`}>
                    {room.type}
                  </span>
                </td>
                <td>
                  <div className="flex" style={{ gap: "0.5rem" }}>
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem" }}
                      onClick={() => handleEdit(room)}
                    >
                      Modifier
                    </button>
                    <button 
                      className="btn-secondary" 
                      style={{ padding: "0.375rem 0.75rem", fontSize: "0.75rem", color: "var(--danger)", borderColor: "var(--danger)" }}
                      onClick={() => handleDelete(room.id)}
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
        title={editingRoom ? 'Modifier la salle' : 'Ajouter une salle'}
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
          <div style={{ marginBottom: "1rem" }}>
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
          <div style={{ marginBottom: "1.5rem" }}>
            <label style={{ display: "block", marginBottom: "0.5rem", fontSize: "0.875rem", fontWeight: 500 }}>
              Type *
            </label>
            <select
              value={formData.type}
              onChange={(e) => setFormData({ ...formData, type: e.target.value })}
              required
              style={{ width: "100%" }}
            >
              <option value="CLASSROOM">Salle de classe</option>
              <option value="LABORATORY">Laboratoire</option>
              <option value="AUDITORIUM">Amphithéâtre</option>
            </select>
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
              {editingRoom ? 'Modifier' : 'Ajouter'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}