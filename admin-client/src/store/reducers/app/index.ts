import { createSlice, PayloadAction } from "@reduxjs/toolkit";

export interface AppState {
  afterPageActionRedirect: string | null;
  currentDataGridSelector: string | null;
}

const initialState: AppState = {
  afterPageActionRedirect: null,
  currentDataGridSelector: null,
};

export const appSlice = createSlice({
  name: "app",
  initialState,
  reducers: {
    setAfterPageActionRedirect(state, action: PayloadAction<string | null>) {
      state.afterPageActionRedirect = action.payload;
    },
    setCurrentDataGridSelector(state, action: PayloadAction<string | null>) {
      state.currentDataGridSelector = action.payload;
    },
  },
});

export const appActions = appSlice.actions;

export const appReducer = appSlice.reducer;
