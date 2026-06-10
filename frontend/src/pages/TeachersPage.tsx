import { useEffect, useState } from "react";
import { api } from "../service/api"

type Teacher = {
  id: number;
  firstName: string;
  lastName: string | null;
  email: string;
  active: boolean;
};

export default function TeachersPage() {
  const [teachers, setTeachers] = useState<Teacher[]>([]);
  const [loading, setLoading] = useState(true);

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

  if (loading) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>Enseignants</h1>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Actif</th>
          </tr>
        </thead>

        <tbody>
          {teachers.map((teacher) => (
            <tr key={teacher.id}>
              <td>{teacher.id}</td>
              <td>{teacher.firstName}</td>
              <td>{teacher.lastName}</td>
              <td>{teacher.email}</td>
              <td>{teacher.active ? "Yes" : "No"}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}