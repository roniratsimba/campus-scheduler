import { useEffect, useState } from "react";
import { api } from "../service/api";
import axios from "axios";

// 1. Définition des interfaces pour typer les données de l'API
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

interface TimeSlot {
  id: number;
  dayOfWeek: string;
  startTime: string;
  endTime: string;
}

interface ScheduleWeek {
  id: number;
  startDate: string;
}

type Props = {
  open: boolean;
  onClose: () => void;
  onCreated: () => void;
};

export default function CourseSessionModal({
  open,
  onClose,
  onCreated,
}: Props) {
  // 2. Typage des tableaux d'états pour éviter le type implicite 'never[]'
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [rooms, setRooms] = useState<Room[]>([]);
  const [groups, setGroups] = useState<AcademicGroup[]>([]);
  const [timeSlots, setTimeSlots] = useState<TimeSlot[]>([]);
  const [weeks, setWeeks] = useState<ScheduleWeek[]>([]);
  
  const [teacherId, setTeacherId] = useState("");
  const [subjectId, setSubjectId] = useState("");
  const [roomId, setRoomId] = useState("");
  const [timeSlotId, setTimeSlotId] = useState("");
  const [academicGroupId, setAcademicGroupId] = useState("");
  const [scheduleWeekId, setScheduleWeekId] = useState("");
  const [deliveryMode, setDeliveryMode] = useState("PRESENTIAL");
  const [status, setStatus] = useState("DRAFT");
  const [errorMessage, setErrorMessage] =
  useState("");

  const handleSubmit = async () => {
    setErrorMessage("");
    try {
      await api.post("/course-sessions", {
        teacherId: Number(teacherId),
        subjectId: Number(subjectId),
        roomId: Number(roomId),
        timeSlotId: Number(timeSlotId),
        academicGroupIds: [Number(academicGroupId)],
        scheduleWeekId: Number(scheduleWeekId),
        deliveryMode,
        status,
      });
      onCreated();
      onClose();
    }catch (error: unknown) {

  if (axios.isAxiosError(error)) {

    console.log(error.response?.data);

    alert(
      error.response?.data?.message ??
      "Erreur lors de la création"
    );

    return;
  }

  alert("Erreur lors de la création");
}
  };

  useEffect(() => {
    if (!open) {
      return;
    }

    Promise.all([
      api.get("/teachers"),
      api.get("/subjects"),
      api.get("/rooms"),
      api.get("/academic-groups"),
      api.get("/timeslots"),
      api.get("/schedule-weeks"),
    ]).then(
      ([
        teachersRes,
        subjectsRes,
        roomsRes,
        groupsRes,
        slotsRes,
        weeksRes,
      ]) => {
        setTeachers(teachersRes.data);
        setSubjects(subjectsRes.data);
        setRooms(roomsRes.data);
        setGroups(groupsRes.data);
        setTimeSlots(slotsRes.data);
        setWeeks(weeksRes.data);
      }
    );
  }, [open]);

  if (!open) {
    return null;
  }

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
        <h2>Nouvelle séance</h2>

        <div style={{ display: "grid", gap: "1rem" }}>
          <select value={teacherId} onChange={(e) => setTeacherId(e.target.value)}>
            <option value="">Choisir un enseignant</option>
            {/* 3. Les types 'any' ont été supprimés, TypeScript utilise le typage des états */}
            {teachers.map((teacher) => (
              <option key={teacher.id} value={teacher.id}>
                {teacher.firstName} {teacher.lastName}
              </option>
            ))}
          </select>

          <select value={subjectId} onChange={(e) => setSubjectId(e.target.value)}>
            <option value="">Choisir une matière</option>
            {subjects.map((subject) => (
              <option key={subject.id} value={subject.id}>
                {subject.name}
              </option>
            ))}
          </select>

          <select value={roomId} onChange={(e) => setRoomId(e.target.value)}>
            <option value="">Choisir une salle</option>
            {rooms.map((room) => (
              <option key={room.id} value={room.id}>
                {room.code}
              </option>
            ))}
          </select>

          <select value={academicGroupId} onChange={(e) => setAcademicGroupId(e.target.value)}>
            <option value="">Choisir un groupe</option>
            {groups.map((group) => (
              <option key={group.id} value={group.id}>
                {group.level} {group.program} G{group.groupNumber}
              </option>
            ))}
          </select>

          <select value={timeSlotId} onChange={(e) => setTimeSlotId(e.target.value)}>
            <option value="">Choisir un créneau</option>
            {timeSlots.map((slot) => (
              <option key={slot.id} value={slot.id}>
                {slot.dayOfWeek} {slot.startTime} - {slot.endTime}
              </option>
            ))}
          </select>

          <select value={scheduleWeekId} onChange={(e) => setScheduleWeekId(e.target.value)}>
            <option value="">Choisir une semaine</option>
            {weeks.map((week) => (
              <option key={week.id} value={week.id}>
                {week.startDate}
              </option>
            ))}
          </select>

          <select value={deliveryMode} onChange={(e) => setDeliveryMode(e.target.value)}>
            <option value="PRESENTIAL">Présentiel</option>
            <option value="ONLINE">En ligne</option>
          </select>

          <select value={status} onChange={(e) => setStatus(e.target.value)}>
            <option value="DRAFT">Brouillon</option>
            <option value="PUBLISHED">Publié</option>
          </select>
            {errorMessage && (
          <div
            style={{
              background: "#fee",
              border: "1px solid #f99",
              color: "#900",
              padding: "0.75rem",
              borderRadius: "4px",
            }}
          >
            ⚠ {errorMessage}
          </div>
)}
          <div style={{ display: "flex", gap: "1rem", marginTop: "1rem" }}>
            <button onClick={onClose}>Annuler</button>
            <button onClick={handleSubmit}>Créer</button>
          </div>
        </div>
      </div>
    </div>
  );
}
