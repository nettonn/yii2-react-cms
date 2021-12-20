import { createSlice, PayloadAction } from "@reduxjs/toolkit";

export interface MainState {
  currentDataGridSelector: string | null;
}

const initialState: MainState = {
  currentDataGridSelector: null,
};

const slice = createSlice({
  name: "main",
  initialState,
  reducers: {
    setCurrentDataGridSelector(state, action: PayloadAction<string | null>) {
      state.currentDataGridSelector = action.payload;
    },
  },
});

export const mainActions = slice.actions;

export const mainReducer = { main: slice.reducer };
