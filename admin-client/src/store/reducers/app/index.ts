import { createSlice, PayloadAction } from "@reduxjs/toolkit";

export interface AppState {
  currentDataGridSelector: string | null;
}

const initialState: AppState = {
  currentDataGridSelector: null,
};

export const appSlice = createSlice({
  name: "app",
  initialState,
  reducers: {
    setCurrentDataGridSelector(state, action: PayloadAction<string | null>) {
      state.currentDataGridSelector = action.payload;
    },
  },
});

export const appActions = appSlice.actions;

export const appReducer = appSlice.reducer;
