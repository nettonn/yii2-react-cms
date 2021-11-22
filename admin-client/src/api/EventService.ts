import { IEvent } from "../models/IEvent";

export default class EventService {
  async getEvents() {
    const eventsString = localStorage.getItem("events") || "[]";
    return JSON.parse(eventsString) as IEvent[];
  }

  async createEvent(event: IEvent) {
    const eventsString = localStorage.getItem("events") || "[]";

    const events = JSON.parse(eventsString) as IEvent[];
    events.push(event);

    localStorage.setItem("events", JSON.stringify(events));
  }
}

export const eventService = new EventService();
