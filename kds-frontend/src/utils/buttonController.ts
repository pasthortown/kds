export interface ButtonAction {
  key: string;
  action: string;
  handler: () => void;
}

export interface ComboAction {
  keys: string[];
  timeWindow: number;
  action: string;
  handler: () => void;
  onProgress?: (progress: number) => void;
}

export class ButtonController {
  private lastActionTime: number = 0;
  private debounceTime: number;
  private actions: ButtonAction[];
  private combos: ComboAction[];

  // Nueva lógica para i/g
  private pendingKey: string | null = null;
  private pendingTimeout: ReturnType<typeof setTimeout> | null = null;
  private readonly COMBO_WAIT_TIME = 1000; // 1 segundo de espera para complemento

  constructor(
    actions: ButtonAction[],
    combos: ComboAction[],
    _onLog: (message: string) => void,
    debounceTime: number = 200
  ) {
    this.actions = actions;
    this.combos = combos;
    this.debounceTime = debounceTime;
    this.setupListeners();
  }

  private setupListeners(): void {
    this.handleKeyDown = this.handleKeyDown.bind(this);
    document.addEventListener('keydown', this.handleKeyDown);
  }

  private handleKeyDown(event: KeyboardEvent): void {
    const key = event.key.toLowerCase();
    const now = Date.now();

    // Prevenir comportamiento por defecto
    event.preventDefault();

    // Lógica especial para i y g (combo de bloqueo)
    if (key === 'i' || key === 'g') {
      this.handleComboKeys(key);
      return;
    }

    // Para otras teclas, ejecutar con debounce normal
    if (now - this.lastActionTime < this.debounceTime) {
      return;
    }

    this.executeSimpleAction(key);
  }

  private handleComboKeys(key: string): void {
    const now = Date.now();

    // Si hay una tecla pendiente
    if (this.pendingKey !== null) {
      // Cancelar el timeout pendiente
      if (this.pendingTimeout) {
        clearTimeout(this.pendingTimeout);
        this.pendingTimeout = null;
      }

      const previousKey = this.pendingKey;
      this.pendingKey = null;

      // Verificar qué combinación tenemos
      if ((previousKey === 'i' && key === 'g') || (previousKey === 'g' && key === 'i')) {
        // Combo i+g o g+i → bloquear pantalla
        console.log('[ButtonController] Combo detectado: bloqueo de pantalla');
        this.executeCombo();
        return;
      }

      if (previousKey === key) {
        // Misma tecla dos veces (i+i o g+g) → ejecutar acción solo 1 vez
        console.log(`[ButtonController] Doble ${key} detectado: ejecutar acción una vez`);
        if (now - this.lastActionTime >= this.debounceTime) {
          this.executeSimpleAction(key);
        }
        return;
      }
    }

    // No hay tecla pendiente, guardar esta y esperar 500ms
    this.pendingKey = key;
    console.log(`[ButtonController] Tecla ${key} recibida, esperando 500ms...`);

    this.pendingTimeout = setTimeout(() => {
      // No llegó complemento, ejecutar la acción de la tecla
      if (this.pendingKey === key) {
        console.log(`[ButtonController] Timeout: ejecutando acción de ${key}`);
        this.pendingKey = null;
        this.pendingTimeout = null;
        this.executeSimpleAction(key);
      }
    }, this.COMBO_WAIT_TIME);
  }

  private executeCombo(): void {
    // Buscar el combo de toggle power (g+i)
    const combo = this.combos.find(c =>
      c.keys.includes('g') && c.keys.includes('i')
    );

    if (combo) {
      if (combo.onProgress) {
        combo.onProgress(100);
      }

      combo.handler();
      this.lastActionTime = Date.now();

      // Resetear progreso después de 1 segundo
      setTimeout(() => {
        if (combo.onProgress) {
          combo.onProgress(0);
        }
      }, 1000);
    }
  }

  private executeSimpleAction(key: string): void {
    const action = this.actions.find((a) => a.key === key);
    if (action) {
      this.lastActionTime = Date.now();
      action.handler();
    }
  }

  public updateActions(actions: ButtonAction[]): void {
    this.actions = actions;
  }

  public updateCombos(combos: ComboAction[]): void {
    this.combos = combos;
  }

  public destroy(): void {
    document.removeEventListener('keydown', this.handleKeyDown);

    if (this.pendingTimeout) {
      clearTimeout(this.pendingTimeout);
    }

    this.pendingKey = null;
  }
}
