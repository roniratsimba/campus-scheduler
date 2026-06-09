import { useEffect, useState } from "react";
import { api } from "../service/api";

type Room = {
  id: number;
  code: string;
  name: string;
  type: string;
};

export default function RoomsPage() {
    const [rooms, setRooms] = useState<Room[]>([]);
    const [loading, setLoading] = useState(true);

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

    if (loading) {
        return <p>Loading...</p>;
    }
    return (
        <div>
            <h1>Rooms</h1>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    {rooms.map((room) => (
                        <tr key={room.id}>
                            <td>{room.id}</td>
                            <td>{room.code}</td>
                            <td>{room.name}</td>
                            <td>{room.type}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}