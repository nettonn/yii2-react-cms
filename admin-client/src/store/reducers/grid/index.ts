import { combineReducers, createSlice, PayloadAction } from "@reduxjs/toolkit";
import { dataGridReducers } from "./grids";

export interface DataGridCommonState {
  currentSelector: string | null;
}

const initialState: DataGridCommonState = {
  currentSelector: null,
};

const slice = createSlice({
  name: "common",
  initialState,
  reducers: {
    setCurrentSelector(state, action: PayloadAction<string | null>) {
      state.currentSelector = action.payload;
    },
  },
});

export const dataGridCommonActions = slice.actions;

export const dataGridCommonReducer = slice.reducer;

export const dataGridReducer = combineReducers({
  common: dataGridCommonReducer,
  ...dataGridReducers,
});
