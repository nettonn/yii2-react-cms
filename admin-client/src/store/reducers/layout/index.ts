import { createSlice, PayloadAction } from "@reduxjs/toolkit";

export interface LayoutState {
  sidebarOpenKeys: string[];
}

const initialState: LayoutState = {
  sidebarOpenKeys: [],
};

const slice = createSlice({
  name: "layout",
  initialState,
  reducers: {
    setSidebarOpenKeys(state, action: PayloadAction<string[]>) {
      state.sidebarOpenKeys = action.payload;
    },
  },
});

export const layoutActions = slice.actions;

export const layoutReducer = slice.reducer;
