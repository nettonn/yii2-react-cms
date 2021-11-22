import createGridSlice from "./createGridSlice";

export const chunkGridSlice = createGridSlice("chunkGrid");

export const chunkGridReducer = chunkGridSlice.reducer;

export const chunkGridActions = chunkGridSlice.actions;
