import { IUser } from "../../../models/IUser";
import { IEvent } from "../../../models/IEvent";
import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { eventActionCreators } from "./action-creators";

export interface EventState {
  guests: IUser[];
  events: IEvent[];
  error: string | null;
}

const initialState: EventState = {
  guests: [],
  events: [],
  error: null,
};

export const eventSlice = createSlice({
  name: "event",
  initialState,
  reducers: {
    setGuests(state, action: PayloadAction<IUser[]>) {
      state.guests = action.payload;
    },
    setEvents(state, action: PayloadAction<IEvent[]>) {
      state.events = action.payload;
    },
    setError(state, action: PayloadAction<string | null>) {
      state.error = action.payload;
    },
  },
  extraReducers: {
    [eventActionCreators.fetchGuests.pending.type]: (state, action) => {
      state.error = null;
    },
    [eventActionCreators.fetchGuests.fulfilled.type]: (state, action) => {
      state.guests = action.payload;
    },
    [eventActionCreators.fetchGuests.rejected.type]: (state, action) => {
      state.error = action.error.message;
    },
    [eventActionCreators.fetchEvents.pending.type]: (state, action) => {
      state.error = null;
    },
    [eventActionCreators.fetchEvents.fulfilled.type]: (state, action) => {
      state.events = action.payload;
    },
    [eventActionCreators.fetchEvents.rejected.type]: (state, action) => {
      state.error = action.error.message;
    },
    [eventActionCreators.createEvent.pending.type]: (state, action) => {
      state.error = null;
    },
    // [eventActionCreators.createEvent.fulfilled.type]: (state, action) => {
    // state.events = action.payload;
    // },
    [eventActionCreators.createEvent.rejected.type]: (state, action) => {
      state.error = action.error.message;
    },
  },
});

export const eventActions = { ...eventSlice.actions, ...eventActionCreators };

export const eventReducer = eventSlice.reducer;
