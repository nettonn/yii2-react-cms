import { createAsyncThunk } from "@reduxjs/toolkit";
import { IUser } from "../../../models/IUser";
import { IEvent } from "../../../models/IEvent";
import { userService } from "../../../api/UserService";
import { eventService } from "../../../api/EventService";

export const eventActionCreators = {
  fetchGuests: createAsyncThunk("event/fetchGuests", async (_, thunkApi) => {
    try {
      const response = await userService.list<IUser>();
      const guests = response.data;
      if (!guests) throw new Error("Error loading guests");
      return guests;
    } catch (e: any) {
      throw new Error(e.message || "There is an error!");
    }
  }),
  fetchEvents: createAsyncThunk<IEvent[], number>(
    "event/fetchEvents",
    async (userId, thunkApi) => {
      const events = await eventService.getEvents();
      if (events.length) {
        return events.filter(
          (event: IEvent) =>
            event.author === userId || event.guests.includes(userId)
        );
      }
      return [];
    }
  ),
  createEvent: createAsyncThunk<void, IEvent>(
    "event/createEvent",
    async (event, thunkApi) => {
      await eventService.createEvent(event);
    }
  ),
};
