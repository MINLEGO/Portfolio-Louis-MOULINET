FROM node:22-alpine AS build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build && npm prune --omit=dev

FROM node:22-alpine
WORKDIR /app
# Port 80, comme l'ancienne image Apache : la configuration Coolify reste valable.
ENV NODE_ENV=production HOST=0.0.0.0 PORT=80
COPY --from=build /app/package.json /app/server.mjs ./
COPY --from=build /app/node_modules ./node_modules
COPY --from=build /app/dist ./dist
USER node
EXPOSE 80
CMD ["node", "server.mjs"]
