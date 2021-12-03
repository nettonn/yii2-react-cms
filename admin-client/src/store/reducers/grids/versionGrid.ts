import createGridSlice from "./createGridSlice";

export const versionGridSlice = createGridSlice("versionGrid");

export const versionGridReducer = versionGridSlice.reducer;

export const versionGridActions = versionGridSlice.actions;
