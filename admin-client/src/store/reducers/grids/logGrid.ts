import createGridSlice from "./createGridSlice";

export const logGridSlice = createGridSlice("logGrid");

export const logGridReducer = logGridSlice.reducer;

export const logGridActions = logGridSlice.actions;
