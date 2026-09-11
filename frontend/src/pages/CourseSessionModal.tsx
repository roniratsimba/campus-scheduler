import { useEffect, useState } from "react";
import { api } from "../service/api";
import axios from "axios";

interface Teacher {
  id: number;
  firstName: string;
  lastName: string;
}

interface Subject {
  id: number;
  name: string;
}

interface Room {
  id: number;
  code: string;
}

interface AcademicGroup {
  id: number;
  level: string;
  program: string;
  groupNumber: number;
}

interface ScheduleWeek {
  id: number;
  startDate: string;
  endDate: string;
  status: string;
}

interface TimeSlot {
  id: number;
  dayOfWeek: string;
  startTime: string;
  endTime: string;
}

type SelectedCell = {
  day: string;
  slot: string;
} | null;

type Props = {
  open: boolean;
  onClose: () => void;
  onCreated: () => void;
  selectedCell: SelectedCell;
  selectedWeekId?: number;
};

export default function CourseSessionModal({
  open,
  onClose,
  onCreated,
  selectedCell,
  selectedWeekId,
}: Props) {
  // États des données issues de l'API
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [rooms, setRooms] = useState<Room[]>([]);
  const [groups, setGroups] = useState<AcademicGroup[]>([]);
  const [scheduleWeeks, setScheduleWeeks] = useState<ScheduleWeek[]>([]);
  const [timeSlots, setTimeSlots] = useState<TimeSlot[]>([]);

  // États du formulaire
  const [teacherId, setTeacherId] = useState("");
  const [subjectId, setSubjectId] = useState("");
  const [roomId, setRoomId] = useState("");
  const [academicGroupId, setAcademicGroupId] = useState("");
  const [scheduleWeekId, setScheduleWeekId] = useState("");
  const [deliveryMode, setDeliveryMode] = useState("PRESENTIAL");
  const [status, setStatus] = useState("DRAFT");
  const [errorMessage, setErrorMessage] = useState("");

  // ✨ CALCUL DIRECT AU RENDU (Plus besoin de useEffect ni de useState pour le slot)
  const currentSlot = selectedCell
    ? timeSlots.find(
        (t) =>
          t.dayOfWeek === selectedCell.day &&
          t.startTime.startsWith(selectedCell.slot)
      )
    : null;

  // Chargement des données à l'ouverture
  useEffect(() => {
    if (!open) return;

    Promise.all([
      api.get("/teachers"),
      api.get("/subjects"),
      api.get("/rooms"),
      api.get("/academic-groups"),
      api.get("/schedule-weeks"),
      api.get("/timeslots"),
    ]).then(([t, s, r, g, w, ts]) => {
      setTeachers(t.data);
      setSubjects(s.data);
      setRooms(r.data);
      setGroups(g.data);
      setScheduleWeeks(w.data);
      setTimeSlots(ts.data);
      if (selectedWeekId) {
        setScheduleWeekId(String(selectedWeekId));
      } else if (w.data.length > 0) {
        setScheduleWeekId(String(w.data[0].id));
      }
    });
  }, [open, selectedWeekId]);

  const handleSubmit = async () => {
    setErrorMessage("");

    // Validation : s'assurer qu'un créneau correspondant existe dans la grille
    if (!currentSlot) {
      setErrorMessage("Aucun créneau horaire correspondant trouvé.");
      return;
    }

    if (!scheduleWeekId) {
      setErrorMessage("Veuillez sélectionner une semaine.");
      return;
    }

    try {
      await api.post("/course-sessions", {
        teacherId: Number(teacherId),
        subjectId: Number(subjectId),
        roomId: Number(roomId),
        timeSlotId: currentSlot.id,
        academicGroupIds: [Number(academicGroupId)],
        scheduleWeekId: Number(scheduleWeekId),
        deliveryMode,
        status,
      });

      onCreated();
      onClose();
    } catch (error: unknown) {
      if (axios.isAxiosError(error)) {
        setErrorMessage(
          error.response?.data?.message ?? "Erreur lors de la création"
        );
        return;
      }
      setErrorMessage("Erreur inconnue");
    }
  };

  if (!open) return null;

  return (
    <div
      style={{
        position: "fixed",
        inset: 0,
        background: "rgba(0,0,0,0.4)",
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
      }}
    >
      <div
        style={{
          background: "white",
          padding: "2rem",
          width: "700px",
          borderRadius: "8px",
        }}
      >
        <h2>
          Nouvelle séance{" "}
          {selectedCell && (
            <small>
              ({selectedCell.day} - {selectedCell.slot})
            </small>
          )}
        </h2>

        {errorMessage && (
          <div
            style={{
              background: "#fee",
              border: "1px solid #f99",
              color: "#900",
              padding: "0.75rem",
              borderRadius: "4px",
              marginBottom: "1rem",
            }}
          >
            ⚠ {errorMessage}
          </div>
        )}

        <div style={{ display: "grid", gap: "1rem" }}>
          <select value={teacherId} onChange={(e) => setTeacherId(e.target.value)}>
            <option value="">Prof</option>
            {teachers.map((t) => (
              <option key={t.id} value={t.id}>
                {t.firstName} {t.lastName}
              </option>
            ))}
          </select>

          <select value={subjectId} onChange={(e) => setSubjectId(e.target.value)}>
            <option value="">Matière</option>
            {subjects.map((s) => (
              <option key={s.id} value={s.id}>
                {s.name}
              </option>
            ))}
          </select>

          <select value={roomId} onChange={(e) => setRoomId(e.target.value)}>
            <option value="">Salle</option>
            {rooms.map((r) => (
              <option key={r.id} value={r.id}>
                {r.code}
              </option>
            ))}
          </select>

          <select value={academicGroupId} onChange={(e) => setAcademicGroupId(e.target.value)}>
            <option value="">Groupe</option>
            {groups.map((g) => (
              <option key={g.id} value={g.id}>
                {g.level} {g.program} G{g.groupNumber}
              </option>
            ))}
          </select>

          <select value={scheduleWeekId} onChange={(e) => setScheduleWeekId(e.target.value)}>
            <option value="">Semaine</option>
            {scheduleWeeks.map((w) => (
              <option key={w.id} value={w.id}>
                {w.startDate} → {w.endDate} ({w.status})
              </option>
            ))}
          </select>

          {/* Affichage dynamique du créneau détecté ou d'un message d'erreur */}
          <select value={currentSlot ? String(currentSlot.id) : ""} disabled>
            <option value={currentSlot ? String(currentSlot.id) : ""}>
              {currentSlot 
                ? `Créneau : ${currentSlot.dayOfWeek} (${currentSlot.startTime} - ${currentSlot.endTime})`
                : "Créneau introuvable pour cette cellule"}
            </option>
          </select>

          <select value={deliveryMode} onChange={(e) => setDeliveryMode(e.target.value)}>
            <option value="PRESENTIAL">Présentiel</option>
            <option value="ONLINE">En ligne</option>
          </select>

          <select value={status} onChange={(e) => setStatus(e.target.value)}>
            <option value="DRAFT">Brouillon</option>
            <option value="PUBLISHED">Publié</option>
          </select>

          <div style={{ display: "flex", gap: "1rem" }}>
            <button onClick={onClose}>Annuler</button>
            <button onClick={handleSubmit} disabled={!currentSlot}>Créer</button>
          </div>
        </div>
      </div>
    </div>
  );
}
