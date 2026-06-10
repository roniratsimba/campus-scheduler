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

  if (loading) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>Semaines d'emploi du temps</h1>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Status</th>
            <th>Publié</th>
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
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}