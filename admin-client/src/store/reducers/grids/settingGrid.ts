import createGridSlice from "./createGridSlice";

export const settingGridSlice = createGridSlice("settingGrid");

export const settingGridReducer = settingGridSlice.reducer;

export const settingGridActions = settingGridSlice.actions;
