# Dockerfile para el servicio sync (.NET 6)
# Este servicio sincroniza órdenes desde MaxPoint y las envía al backend KDS

FROM mcr.microsoft.com/dotnet/sdk:6.0 AS build
WORKDIR /src

# Copiar archivos del proyecto
COPY *.csproj ./
RUN dotnet restore

# Copiar el resto del código y compilar
COPY . ./
RUN dotnet publish -c Release -o /app

# Imagen final
FROM mcr.microsoft.com/dotnet/aspnet:6.0
WORKDIR /app

# Copiar la aplicación compilada
COPY --from=build /app .

# Crear directorio para logs
RUN mkdir -p /app/logs

# Puerto de la API
EXPOSE 8100

# Variables de entorno
ENV ASPNETCORE_URLS=http://+:8100
ENV DOTNET_RUNNING_IN_CONTAINER=true

# Comando de inicio
ENTRYPOINT ["dotnet", "KDS.dll"]
