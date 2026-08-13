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

interface Props {
    id: string;
}

interface OrderResult {
    type: 'success' | 'error';
    message: string;
}

export default function EventShow({ id }: Props) {
    const [event, setEvent] = useState<EventItem | null>(null);
    const [loading, setLoading] = useState(true);
    const [quantities, setQuantities] = useState<Record<number, number>>({});
    const [buying, setBuying] = useState<number | null>(null);
    const [results, setResults] = useState<Record<number, OrderResult>>({});

    const fetchEvent = () => {
        fetch(`/api/events/${id}`)
            .then((res) => res.json())
            .then((json) => {
                setEvent(json.data);
                setLoading(false);
            });
    };

    useEffect(() => {
        fetchEvent();
    }, [id]);

    const handleBuy = async (ticket: Ticket) => {
        const quantity = quantities[ticket.id] || 1;
        setBuying(ticket.id);

        try {
            const res = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    event_id: event?.id,
                    ticket_id: ticket.id,
                    quantity,
                }),
            });

            const json = await res.json();

            if (res.status === 201) {
                setResults((prev) => ({
                    ...prev,
                    [ticket.id]: {
                        type: 'success',
                        message: `Order #${json.data.id} confirmed — total $${json.data.total_price}`,
                    },
                }));
                fetchEvent(); // refresh quantity_available
            } else {
                const firstError = json.errors ? Object.values(json.errors)[0] : null;
                const message = Array.isArray(firstError) ? firstError[0] : (json.message ?? 'Order failed.');
                setResults((prev) => ({
                    ...prev,
                    [ticket.id]: { type: 'error', message: String(message) },
                }));
            }
        } catch {
            setResults((prev) => ({
                ...prev,
                [ticket.id]: { type: 'error', message: 'Something went wrong. Try again.' },
            }));
        } finally {
            setBuying(null);
        }
    };

    if (loading) return <div className="p-6">Loading...</div>;
    if (!event) return <div className="p-6">Event not found.</div>;

    return (
        <div className="p-6 space-y-4">
            <Link href="/events" className="text-sm text-blue-600 underline">
                ← Back to all events
            </Link>

            <h1 className="text-2xl font-bold">{event.name}</h1>
            <p className="text-gray-600">{event.description}</p>
            <p className="text-sm text-gray-500">{event.venue} — {event.city}</p>
            <p className="text-sm">{new Date(event.starts_at).toLocaleString()}</p>

            <h2 className="text-xl font-semibold mt-6">Tickets</h2>
            <div className="space-y-3">
                {event.tickets?.map((ticket) => (
                    <div key={ticket.id} className="border rounded-lg p-4 space-y-2">
                        <p className="font-medium">{ticket.type}</p>
                        <p className="text-sm">${ticket.price} — {ticket.quantity_available} available</p>

                        <div className="flex items-center gap-2">
                            <input
                                type="number"
                                min={1}
                                max={ticket.quantity_available}
                                value={quantities[ticket.id] ?? 1}
                                onChange={(e) =>
                                    setQuantities((prev) => ({ ...prev, [ticket.id]: Number(e.target.value) }))
                                }
                                className="w-16 border rounded px-2 py-1 text-sm"
                            />
                            <button
                                onClick={() => handleBuy(ticket)}
                                disabled={buying === ticket.id || ticket.quantity_available === 0}
                                className="bg-black text-white text-sm px-3 py-1 rounded disabled:opacity-50"
                            >
                                {buying === ticket.id ? 'Processing...' : 'Buy'}
                            </button>
                        </div>

                        {results[ticket.id] && (
                            <p
                                className={`text-sm ${
                                    results[ticket.id].type === 'success' ? 'text-green-600' : 'text-red-600'
                                }`}
                            >
                                {results[ticket.id].message}
                            </p>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
}