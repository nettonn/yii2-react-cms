import createGridSlice from "./createGridSlice";

export const menuGridSlice = createGridSlice("menuGrid");

export const menuGridReducer = menuGridSlice.reducer;

export const menuGridActions = menuGridSlice.actions;
