import { useEffect, useState } from "react";
import { api } from "../service/api";

type AcademicGroup = {
  id: number;
  groupNumber: number;
  level: string;
  program: string;
};

export default function AcademicGroupsPage() {
  const [groups, setGroups] = useState<AcademicGroup[]>([]);
  const [loading, setLoading] = useState(true);

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

  if (loading) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1>Groupes académiques</h1>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Level</th>
            <th>Program</th>
            <th>Group</th>
          </tr>
        </thead>

        <tbody>
          {groups.map((group) => (
            <tr key={group.id}>
              <td>{group.id}</td>
              <td>{group.level}</td>
              <td>{group.program}</td>
              <td>G{group.groupNumber}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}