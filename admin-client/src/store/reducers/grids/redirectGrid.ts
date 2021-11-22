import createGridSlice from "./createGridSlice";

export const redirectGridSlice = createGridSlice("redirectGrid");

export const redirectGridReducer = redirectGridSlice.reducer;

export const redirectGridActions = redirectGridSlice.actions;
