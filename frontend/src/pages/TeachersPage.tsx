import { useEffect, useState } from 'react';
import api from '../api/axios';
import type { Teacher } from '../types/Teacher';

export default function TeachersPage() {
    const [teachers, setTeachers] = useState<Teacher[]>([]);

    useEffect(() => {
        api.get('/teachers')
            .then((response) => setTeachers(response.data))
            .catch(console.error);
    }, []);

    return (
        <div>
            <h1>Teachers</h1>

            <ul>
                {teachers.map((teacher) => (
                    <li key={teacher.id}>
                        {teacher.firstName} {teacher.lastName}
                    </li>
                ))}
            </ul>
        </div>
    );
}