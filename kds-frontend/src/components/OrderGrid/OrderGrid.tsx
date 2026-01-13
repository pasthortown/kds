import { useEffect, useMemo, useCallback, useRef, useState } from 'react';
import { OrderCard } from '../OrderCard';
import { useOrderStore } from '../../store/orderStore';
import { useAppearance, usePreference } from '../../store/configStore';
import { socketService } from '../../services/socket';
import type { Order, OrderItem } from '../../types';
import { useScreenSize } from '../../hooks/useScreenSize';

interface ColumnCard {
  order: Order;
  items: OrderItem[];
  partNumber: number;
  totalParts: number;
  isFirstPart: boolean;
  isLastPart: boolean;
}

export function OrderGrid() {
  const appearance = useAppearance();
  const preference = usePreference();
  const columnsPerScreen = appearance?.columnsPerScreen || 4;
  const screenSplit = appearance?.screenSplit ?? true;
  const touchEnabled = preference?.touchEnabled ?? false;

  // Obtener la altura del footer desde appearance o usar default
  const footerHeight = parseInt(appearance?.footerHeight || '72', 10);

  // Usar hook para detectar tamaño de pantalla y calcular altura disponible
  // Header tiene padding de 8px*2 + contenido ~40px = ~56px, pero mediremos dinámicamente
  const gridContainerRef = useRef<HTMLDivElement>(null);
  const [measuredHeight, setMeasuredHeight] = useState<number | null>(null);

  // Detectar dimensiones de pantalla
  const screenDimensions = useScreenSize(56, footerHeight);

  // Estado para altura real del grid medida
  const [actualGridHeight, setActualGridHeight] = useState<number | null>(null);

  // Medir la altura real disponible del contenedor
  useEffect(() => {
    const measureHeight = () => {
      // Buscar header y footer en todo el documento
      const headerEl = document.querySelector('header');
      const footerEl = document.querySelector('footer');

      const headerHeight = headerEl ? headerEl.getBoundingClientRect().height : 56;
      const footerHeightActual = footerEl ? footerEl.getBoundingClientRect().height : footerHeight;

      const available = window.innerHeight - headerHeight - footerHeightActual;

      // También medir el contenedor del grid directamente
      const gridEl = gridContainerRef.current;
      const gridRealHeight = gridEl ? gridEl.getBoundingClientRect().height : null;

      setMeasuredHeight(available);
      if (gridRealHeight) {
        setActualGridHeight(gridRealHeight);
      }
    };

    measureHeight();
    window.addEventListener('resize', measureHeight);

    // También medir después de un pequeño delay para asegurar que el DOM está listo
    const timeoutId = setTimeout(measureHeight, 100);
    // Y otra vez más tarde por si hay re-renders
    const timeoutId2 = setTimeout(measureHeight, 500);

    return () => {
      window.removeEventListener('resize', measureHeight);
      clearTimeout(timeoutId);
      clearTimeout(timeoutId2);
    };
  }, [footerHeight]);

  // Altura final a usar: PREFERIR la altura real del grid si está disponible
  // porque es la medición más precisa del espacio real disponible
  const availableHeight = actualGridHeight || measuredHeight || screenDimensions.availableHeight;

  // Mapeo preciso de tamaños de fuente a PÍXELES reales (sincronizado con OrderCard +4px)
  const productFontSizes: Record<string, number> = {
    xsmall: 14, small: 16, medium: 18, large: 20, xlarge: 24, xxlarge: 28,
  };
  const modifierFontSizes: Record<string, number> = {
    xsmall: 17, small: 18, medium: 19, large: 20, xlarge: 22, xxlarge: 24,
  };

  // Obtener tamaños de fuente configurados
  const productFontSize = appearance?.productFontSize || 'medium';
  const modifierFontSize = appearance?.modifierFontSize || 'small';

  // Calcular altura REAL de un item producto base
  // padding: 4px arriba + 4px abajo = 8px
  // lineHeight: 1.3
  const productLineHeight = 1.3;
  const productPadding = 8; // 4px arriba + 4px abajo
  const itemProductHeight = Math.ceil(
    (productFontSizes[productFontSize] || 14) * productLineHeight + productPadding
  );

  // Calcular altura REAL de una línea de modificador
  // marginTop: 2px
  // lineHeight: 1.4
  const modifierLineHeight = 1.4;
  const modifierMarginTop = 2;
  const itemModifierLineHeight = Math.ceil(
    (modifierFontSizes[modifierFontSize] || 11) * modifierLineHeight + modifierMarginTop
  );

  // Grid padding (p-4 = 16px * 2 lados = 32px vertical)
  const gridPadding = 32;

  // Tamaños de fuente del header (sincronizados con OrderCard getFontSize)
  const headerFontSizes: Record<string, number> = {
    xsmall: 14, small: 16, medium: 18, large: 20, xlarge: 24, xxlarge: 28,
  };
  const timerFontSizes: Record<string, number> = {
    xsmall: 14, small: 16, medium: 18, large: 20, xlarge: 24, xxlarge: 28,
  };
  const clientFontSizes: Record<string, number> = {
    xsmall: 14, small: 15, medium: 16, large: 18, xlarge: 20, xxlarge: 22,
  };
  const channelFontSizes: Record<string, number> = {
    xsmall: 13, small: 14, medium: 15, large: 16, xlarge: 18, xxlarge: 20,
  };

  // Obtener tamaños de fuente configurados para header/footer
  const headerFontSizeConfig = appearance?.headerFontSize || 'medium';
  const timerFontSizeConfig = appearance?.timerFontSize || 'medium';
  const clientFontSizeConfig = appearance?.clientFontSize || 'small';
  const channelFontSizeConfig = appearance?.channelFontSize || 'small';

  // Calcular altura dinámica del header de la tarjeta
  // Row 1 (order + timer): padding 8px*2 + max(header, timer font)
  const headerRowFontHeight = Math.max(
    headerFontSizes[headerFontSizeConfig] || 18,
    timerFontSizes[timerFontSizeConfig] || 18
  );
  const headerRowHeight = 16 + headerRowFontHeight; // padding 8px top + 8px bottom
  // Row 2 (client): padding 6px bottom + font
  const clientRowHeight = 6 + (clientFontSizes[clientFontSizeConfig] || 15);
  // Total header (ambas filas si se muestra cliente)
  const orderHeaderHeight = headerRowHeight + clientRowHeight;

  // Calcular altura dinámica del footer de la tarjeta
  // padding 8px*2 + font + extra para channelType badge (~6px)
  const channelFontHeight = channelFontSizes[channelFontSizeConfig] || 15;
  const channelFooterHeight = 16 + channelFontHeight + 6; // padding + font + badge margin

  // Items container: padding 8px arriba, 0 abajo = 8px
  const itemsContainerPadding = 8;
  // Clip-path margin para splits (reducido a 12px)
  const clipPathMargin = 12;
  // Border de la tarjeta (3px * 2)
  const cardBorder = 6;

  // Altura real disponible para cards (restando grid padding)
  const cardAvailableHeight = availableHeight - gridPadding;

  // Altura disponible para items en orden completa (sin split)
  const singleOrderItemsHeight =
    cardAvailableHeight - orderHeaderHeight - channelFooterHeight - itemsContainerPadding - cardBorder;

  // Altura para PRIMERA parte de split (header + items, sin footer)
  const firstPartOfSplitItemsHeight =
    cardAvailableHeight - orderHeaderHeight - clipPathMargin - itemsContainerPadding - cardBorder;

  // Altura para OTRAS partes de split (sin header, con footer en última)
  const otherPartsItemsHeight =
    cardAvailableHeight - clipPathMargin - channelFooterHeight - itemsContainerPadding - cardBorder;

  // Calcular cuántos items caben basado en altura REAL en píxeles
  // Para decidir si dividir, usamos la altura de orden completa (sin split)
  // Si excede, usamos la altura de primera parte de split
  const dynamicSingleOrderMaxHeight = Math.max(itemProductHeight * 2, singleOrderItemsHeight);
  const dynamicFirstPartOfSplitMaxHeight = Math.max(itemProductHeight * 2, firstPartOfSplitItemsHeight);
  const dynamicOtherPartsMaxHeight = Math.max(itemProductHeight * 2, otherPartsItemsHeight);

  // Obtener TODAS las órdenes pendientes (no paginar aquí)
  const allOrders = useOrderStore((state) => state.orders);
  const currentPage = useOrderStore((state) => state.currentPage);
  const { setLastFinished } = useOrderStore();

  // Handler para finalizar orden via touch/click
  const handleFinishOrder = useCallback((orderId: string) => {
    socketService.finishOrder(orderId);
    setLastFinished(orderId);
  }, [setLastFinished]);

  // Estimar caracteres por línea basado en el ancho de columna
  // Con 4 columnas en ~1280px, cada columna es ~300px - padding
  // Con fuente xlarge (24px), aproximadamente 12-15 caracteres por línea
  const charsPerLineProduct = 15; // Conservador para productos
  const charsPerLineModifier = 20; // Modificadores tienen fuente más pequeña

  // Obtener configuración de visibilidad de campos
  const showSubitems = appearance?.showSubitems !== false;
  const showModifiers = appearance?.showModifiers !== false;
  const showNotes = appearance?.showNotes !== false;
  const showComments = appearance?.showComments !== false;

  // Calcular la altura REAL en píxeles de un item (considerando modificadores, notas y comentarios)
  // SOLO si están visibles según la configuración de apariencia
  const calculateItemHeight = (item: OrderItem): number => {
    // Altura del producto - considerar text wrapping para nombres largos
    const productNameLength = item.name?.length || 0;
    const productLines = Math.max(1, Math.ceil(productNameLength / charsPerLineProduct));
    let height = productLines * itemProductHeight;

    // Altura de notas especiales - justo después del producto
    // Solo incluir si showNotes está habilitado
    if (showNotes && item.notes) {
      const notesLength = item.notes.length;
      const commaCount = (item.notes.match(/,/g) || []).length;
      const estimatedLines = Math.max(1, Math.ceil(notesLength / charsPerLineModifier), commaCount + 1);
      height += estimatedLines * itemModifierLineHeight;
    }

    // Altura de subitems - cada subitem ocupa una línea
    // Solo incluir si showSubitems está habilitado
    if (showSubitems && 'subitems' in item) {
      const subitems = (item as unknown as { subitems: Array<{ name: string; quantity: number }> }).subitems;
      if (Array.isArray(subitems)) {
        height += subitems.length * itemModifierLineHeight;
      }
    }

    // Altura de modificadores (cada línea separada por coma, también con posible wrap)
    // Solo incluir si showModifiers está habilitado
    if (showModifiers && item.modifier) {
      const modifierParts = item.modifier.split(',');
      let modifierTotalLines = 0;
      for (const mod of modifierParts) {
        const modLength = mod.trim().length;
        const modLines = Math.max(1, Math.ceil(modLength / charsPerLineModifier));
        modifierTotalLines += modLines;
      }
      height += modifierTotalLines * itemModifierLineHeight;
    }

    // Altura de comentarios - similar estimación
    // Solo incluir si showComments está habilitado
    if (showComments && item.comments) {
      const commentsLength = item.comments.length;
      const estimatedLines = Math.max(1, Math.ceil(commentsLength / charsPerLineModifier));
      height += estimatedLines * itemModifierLineHeight;
    }

    return height;
  };

  // Calcular cuántos items caben dada una altura máxima en píxeles
  const getItemsForHeight = (
    items: OrderItem[],
    maxHeight: number,
    startIndex: number = 0
  ): OrderItem[] => {
    const result: OrderItem[] = [];
    let currentHeight = 0;

    for (let i = startIndex; i < items.length; i++) {
      const itemHeight = calculateItemHeight(items[i]);

      // Si agregar este item excede la altura Y ya tenemos al menos un item, cortar aquí
      if (currentHeight + itemHeight > maxHeight && result.length > 0) {
        break;
      }

      result.push(items[i]);
      currentHeight += itemHeight;
    }

    return result;
  };

  // Calcular TODAS las columnas (con split de órdenes largas)
  const allColumns = useMemo((): ColumnCard[] => {
    const columns: ColumnCard[] = [];

    for (const order of allOrders) {

      // Calcular altura total de la orden en píxeles
      const totalHeight = order.items.reduce((sum, item) => sum + calculateItemHeight(item), 0);

      // Decidir si necesita split comparando con altura de orden completa (con footer)
      const needsSplit = screenSplit && totalHeight > dynamicSingleOrderMaxHeight;

      if (!needsSplit) {
        // La orden completa cabe en una sola tarjeta (tiene header + items + footer)
        columns.push({
          order,
          items: order.items,
          partNumber: 1,
          totalParts: 1,
          isFirstPart: true,
          isLastPart: true,
        });
      } else {
        // Dividir la orden en partes basándose en altura real en píxeles
        const parts: OrderItem[][] = [];
        let remainingItems = [...order.items];
        let isFirst = true;

        while (remainingItems.length > 0) {
          // Primera parte: header + items + clip-path (SIN footer)
          // Otras partes: clip-path + items + footer (última) o clip-path
          const maxHeight = isFirst ? dynamicFirstPartOfSplitMaxHeight : dynamicOtherPartsMaxHeight;
          const partItems = getItemsForHeight(remainingItems, maxHeight);

          // Evitar partes vacías
          if (partItems.length === 0 && remainingItems.length > 0) {
            // Si no cabe ni un item, forzar al menos uno
            partItems.push(remainingItems[0]);
          }

          parts.push(partItems);
          remainingItems = remainingItems.slice(partItems.length);
          isFirst = false;
        }

        // Agregar TODAS las partes (la paginación se maneja después)
        for (let i = 0; i < parts.length; i++) {
          columns.push({
            order,
            items: parts[i],
            partNumber: i + 1,
            totalParts: parts.length,
            isFirstPart: i === 0,
            isLastPart: i === parts.length - 1,
          });
        }
      }
    }

    return columns;
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [
    allOrders,
    screenSplit,
    dynamicSingleOrderMaxHeight,
    dynamicFirstPartOfSplitMaxHeight,
    dynamicOtherPartsMaxHeight,
    productFontSize,
    modifierFontSize,
    itemProductHeight,
    itemModifierLineHeight,
    showSubitems,
    showModifiers,
    showNotes,
    showComments,
  ]);

  // Paginar columnas de manera simple: SIEMPRE llenar todas las columnas por página
  // Las órdenes PUEDEN dividirse entre páginas - la continuidad visual ya existe
  const displayColumns = useMemo((): ColumnCard[] => {
    const startIndex = (currentPage - 1) * columnsPerScreen;
    const endIndex = startIndex + columnsPerScreen;
    return allColumns.slice(startIndex, endIndex);
  }, [allColumns, currentPage, columnsPerScreen]);

  // Calcular total de páginas basándose en columnas totales
  const totalPages = Math.max(1, Math.ceil(allColumns.length / columnsPerScreen));

  // Actualizar totalPages en el store basándose en columnas
  useEffect(() => {
    const { setTotalPages } = useOrderStore.getState();
    if (setTotalPages) {
      setTotalPages(totalPages);
    }
  }, [totalPages]);

  // Debug panel para mostrar cálculos (presionar 'd' para mostrar/ocultar)
  const [showDebug, setShowDebug] = useState(false);
  useEffect(() => {
    const handleKeyPress = (e: KeyboardEvent) => {
      if (e.key === 'd' && e.ctrlKey) {
        setShowDebug(prev => !prev);
      }
    };
    window.addEventListener('keydown', handleKeyPress);
    return () => window.removeEventListener('keydown', handleKeyPress);
  }, []);

  const debugInfo = {
    windowHeight: typeof window !== 'undefined' ? window.innerHeight : 0,
    actualGridHeight,
    measuredHeight,
    availableHeight,
    gridPadding,
    cardAvailableHeight,
    orderHeaderHeight,
    channelFooterHeight,
    singleOrderItemsHeight,
    dynamicSingleOrderMaxHeight,
    itemProductHeight,
    itemModifierLineHeight,
    screenSplit,
  };

  if (displayColumns.length === 0) {
    return (
      <div
        ref={gridContainerRef}
        className="flex-1 flex items-center justify-center"
        style={{
          minHeight: 0,
        }}
      >
        {showDebug && (
          <div style={{ position: 'fixed', top: 80, left: 10, background: 'rgba(0,0,0,0.9)', color: '#0f0', padding: 10, fontSize: 11, zIndex: 9999, fontFamily: 'monospace' }}>
            <pre>{JSON.stringify(debugInfo, null, 2)}</pre>
          </div>
        )}
        <div className="text-center">
          <div className="text-6xl mb-4 opacity-30">
            <svg
              className="w-24 h-24 mx-auto"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={1}
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
              />
            </svg>
          </div>
          <p className="text-gray-500 text-xl">Sin comandas pendientes</p>
        </div>
      </div>
    );
  }

  return (
    <div
      ref={gridContainerRef}
      className="flex-1 p-4"
      style={{
        display: 'grid',
        gridTemplateColumns: `repeat(${columnsPerScreen}, 1fr)`,
        gridTemplateRows: '1fr',
        gap: '1rem',
        maxWidth: '100%',
        minHeight: 0, // Importante para que flex-1 funcione correctamente con grid
        maxHeight: '100%', // Limitar altura máxima al contenedor
        overflow: 'hidden', // Ocultar overflow del grid, pero las tarjetas tienen scroll interno
        alignItems: 'stretch', // Estirar tarjetas al 100% del alto
      }}
    >
      {/* Debug Panel - Ctrl+D para mostrar/ocultar */}
      {showDebug && (
        <div style={{
          position: 'fixed',
          top: 80,
          left: 10,
          background: 'rgba(0,0,0,0.95)',
          color: '#0f0',
          padding: 12,
          fontSize: 11,
          zIndex: 9999,
          fontFamily: 'monospace',
          borderRadius: 8,
          maxWidth: 350,
        }}>
          <div style={{ marginBottom: 8, color: '#ff0', fontWeight: 'bold' }}>DEBUG (Ctrl+D to hide)</div>
          <pre style={{ margin: 0 }}>{JSON.stringify(debugInfo, null, 2)}</pre>
          <div style={{ marginTop: 8, color: '#888', fontSize: 10 }}>
            Order 1 items: {displayColumns[0]?.items.length || 0} |
            Total height calc: {displayColumns[0]?.items.reduce((sum, item) => {
              let h = itemProductHeight;
              if (item.modifier) h += item.modifier.split(',').length * itemModifierLineHeight;
              return sum + h;
            }, 0) || 0}px
          </div>
        </div>
      )}
      {displayColumns.map((column, index) => (
        <OrderCard
          key={`${column.order.id}-${column.partNumber}`}
          order={column.order}
          items={column.items}
          index={index}
          partNumber={column.partNumber}
          totalParts={column.totalParts}
          isFirstPart={column.isFirstPart}
          isLastPart={column.isLastPart}
          appearance={appearance || undefined}
          showIdentifier={preference?.showIdentifier ?? true}
          identifierMessage={preference?.identifierMessage || 'Orden'}
          showName={preference?.showName ?? true}
          onFinish={handleFinishOrder}
          touchEnabled={touchEnabled}
        />
      ))}

      {/* Columnas vacías */}
      {Array.from({ length: columnsPerScreen - displayColumns.length }).map((_, i) => (
        <div
          key={`empty-${i}`}
          style={{
            background: 'rgba(255,255,255,0.03)',
            borderRadius: '8px',
            border: '2px dashed rgba(255,255,255,0.1)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            color: 'rgba(255,255,255,0.2)',
            fontSize: '14px',
          }}
        >
          Sin orden
        </div>
      ))}
    </div>
  );
}
