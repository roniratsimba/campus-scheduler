import { useEffect, useState } from "react";
import { api } from "../service/api";

type Subject = {
  id: number;
  code: string;
  name: string;
};

export default function SubjectsPage() {
  const [subjects, setSubjects] = useState<Subject[]>([]);
  const [loading, setLoading] = useState(true);

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

  if (loading) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>Matière</h1>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Name</th>
          </tr>
        </thead>

        <tbody>
          {subjects.map((subject) => (
            <tr key={subject.id}>
              <td>{subject.id}</td>
              <td>{subject.code}</td>
              <td>{subject.name}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}