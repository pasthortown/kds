import swaggerJsdoc from 'swagger-jsdoc';
import swaggerUi from 'swagger-ui-express';
import { Express } from 'express';

const options: swaggerJsdoc.Options = {
  definition: {
    openapi: '3.0.0',
    info: {
      title: 'KDS Backend API',
      version: '2.0.0',
      description: `
## Sistema KDS - API REST

API para el sistema Kitchen Display System (KDS).

### Modos de operación:
- **ticketMode: API** - Recibe órdenes vía HTTP
- **printMode: CENTRALIZED** - Impresión vía servicio NetCore externo

### Autenticación:
La mayoría de endpoints requieren autenticación JWT. Usar el endpoint \`/api/auth/login\` para obtener el token.

### Endpoints públicos (sin auth):
- \`GET /api/config/health\` - Health check
- \`GET /api/screens/by-number/:number\` - Config de pantalla por número
- \`GET /api/channels/active\` - Canales activos
      `,
      contact: {
        name: 'KFC Ecuador - DSI',
      },
    },
    servers: [
      {
        url: '/api',
        description: 'API Server',
      },
    ],
    components: {
      securitySchemes: {
        bearerAuth: {
          type: 'http',
          scheme: 'bearer',
          bearerFormat: 'JWT',
        },
      },
      schemas: {
        // Auth
        LoginRequest: {
          type: 'object',
          required: ['email', 'password'],
          properties: {
            email: { type: 'string', example: 'admin@kfc.com.ec' },
            password: { type: 'string', example: 'cx-dsi2025' },
          },
        },
        LoginResponse: {
          type: 'object',
          properties: {
            accessToken: { type: 'string' },
            refreshToken: { type: 'string' },
            user: {
              type: 'object',
              properties: {
                userId: { type: 'string' },
                email: { type: 'string' },
                role: { type: 'string', enum: ['ADMIN', 'OPERATOR', 'VIEWER'] },
              },
            },
          },
        },
        // Tickets/Orders
        ApiComanda: {
          type: 'object',
          required: ['id', 'orderId', 'createdAt', 'channel', 'cashRegister', 'products'],
          properties: {
            id: { type: 'string', description: 'ID único externo', example: 'ORD-12345' },
            orderId: { type: 'string', description: 'Número de orden', example: '12345' },
            createdAt: { type: 'string', format: 'date-time', description: 'Fecha (se ignora, se usa hora del servidor)' },
            channel: {
              type: 'object',
              required: ['id', 'name', 'type'],
              properties: {
                id: { type: 'number', example: 1 },
                name: { type: 'string', example: 'LOCAL' },
                type: { type: 'string', example: 'LOCAL' },
              },
            },
            cashRegister: {
              type: 'object',
              properties: {
                cashier: { type: 'string', example: 'CAJERO1' },
                name: { type: 'string', example: 'Caja 1' },
              },
            },
            customer: {
              type: 'object',
              properties: {
                name: { type: 'string', example: 'Juan Pérez' },
              },
            },
            products: {
              type: 'array',
              items: { $ref: '#/components/schemas/ApiProduct' },
            },
            otrosDatos: {
              type: 'object',
              properties: {
                turno: { type: 'number' },
                nroCheque: { type: 'string' },
                llamarPor: { type: 'string' },
                Fecha: { type: 'string' },
                Direccion: { type: 'string' },
              },
            },
            comments: { type: 'string', description: 'Comentarios de la orden' },
            statusPos: { type: 'string', description: 'Estado en el POS' },
            templateHTML: { type: 'string', description: 'Template HTML personalizado' },
            valuesHTML: { type: 'string', description: 'Valores JSON para el template' },
          },
        },
        ApiProduct: {
          type: 'object',
          required: ['name', 'amount'],
          properties: {
            productId: { type: 'string', example: 'PROD001' },
            name: { type: 'string', example: 'COMBO FAMILIAR' },
            amount: { type: 'number', example: 1 },
            category: { type: 'string', example: 'COMBOS' },
            modifier: { type: 'string', example: 'Tamaño Grande' },
            content: {
              type: 'array',
              items: { type: 'string' },
              example: ['*SIN CEBOLLA', '*EXTRA QUESO'],
            },
            comments: { type: 'string' },
          },
        },
        Order: {
          type: 'object',
          properties: {
            id: { type: 'string' },
            externalId: { type: 'string' },
            orderNumber: { type: 'string' },
            status: { type: 'string', enum: ['PENDING', 'IN_PROGRESS', 'FINISHED', 'CANCELLED'] },
            channel: { type: 'string' },
            customerName: { type: 'string' },
            queueName: { type: 'string' },
            screenName: { type: 'string' },
            createdAt: { type: 'string', format: 'date-time' },
            finishedAt: { type: 'string', format: 'date-time' },
            items: {
              type: 'array',
              items: {
                type: 'object',
                properties: {
                  id: { type: 'string' },
                  name: { type: 'string' },
                  quantity: { type: 'number' },
                  modifiers: { type: 'array', items: { type: 'string' } },
                },
              },
            },
          },
        },
        // Config
        HealthCheck: {
          type: 'object',
          properties: {
            status: { type: 'string', example: 'healthy' },
            timestamp: { type: 'string', format: 'date-time' },
            checks: {
              type: 'object',
              properties: {
                database: { type: 'boolean' },
                redis: { type: 'boolean' },
                websocket: { type: 'boolean' },
              },
            },
          },
        },
        ConfigModes: {
          type: 'object',
          properties: {
            ticketMode: { type: 'string', enum: ['API', 'POLLING'] },
            printMode: { type: 'string', enum: ['LOCAL', 'CENTRALIZED'] },
            centralizedPrintUrl: { type: 'string' },
            centralizedPrintPort: { type: 'number' },
          },
        },
        // Screens
        Screen: {
          type: 'object',
          properties: {
            id: { type: 'string' },
            name: { type: 'string' },
            number: { type: 'number' },
            status: { type: 'string', enum: ['ONLINE', 'OFFLINE', 'STANDBY'] },
            queue: {
              type: 'object',
              properties: {
                id: { type: 'string' },
                name: { type: 'string' },
              },
            },
            printer: {
              type: 'object',
              properties: {
                name: { type: 'string' },
                ip: { type: 'string' },
                port: { type: 'number' },
                enabled: { type: 'boolean' },
              },
            },
          },
        },
        // Error
        Error: {
          type: 'object',
          properties: {
            error: { type: 'string' },
            message: { type: 'string' },
          },
        },
        Success: {
          type: 'object',
          properties: {
            success: { type: 'boolean' },
            message: { type: 'string' },
          },
        },
      },
    },
    security: [{ bearerAuth: [] }],
  },
  apis: ['./src/routes/*.ts', './dist/routes/*.js'],
};

const swaggerSpec = swaggerJsdoc(options);

export function setupSwagger(app: Express): void {
  // Swagger UI
  app.use('/api/docs', swaggerUi.serve, swaggerUi.setup(swaggerSpec, {
    customSiteTitle: 'KDS API Documentation',
    customCss: '.swagger-ui .topbar { display: none }',
  }));

  // JSON spec
  app.get('/api/docs.json', (_req, res) => {
    res.setHeader('Content-Type', 'application/json');
    res.send(swaggerSpec);
  });
}
