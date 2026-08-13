import { useEffect, useState } from 'react';
import { Link } from '@inertiajs/react';

interface Ticket {
    id: number;
    type: string;
    price: string;
    quantity_available: number;
}

interface EventItem {
    id: number;
    name: string;
    description: string;
    venue: string;
    city: string;
    starts_at: string;
    tickets?: Ticket[];
}

export default function EventsIndex() {
    const [events, setEvents] = useState<EventItem[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const queryString = window.location.search;
        fetch(`/api/events${queryString}`)
            .then((res) => res.json())
            .then((json) => {
                setEvents(json.data);
                setLoading(false);
            });
    }, []);

    if (loading) return <div className="p-6">Loading...</div>;

    return (
        <div className="p-6 space-y-4">
            <h1 className="text-2xl font-bold">Events</h1>
            {events.map((event) => (
                <Link
                    key={event.id}
                    href={`/events/${event.id}`}
                    className="block border rounded-lg p-4 hover:bg-gray-50"
                >
                    <h2 className="text-lg font-semibold">{event.name}</h2>
                    <p className="text-sm text-gray-500">{event.venue} — {event.city}</p>
                    <p className="text-sm">{new Date(event.starts_at).toLocaleString()}</p>
                </Link>
            ))}
        </div>
    );
}