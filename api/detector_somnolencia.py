import cv2
import mediapipe as mp
import numpy as np
import time
import winsound
import threading


# ============================================================
# CONFIGURACIÓN
# ============================================================

CAMARA = 0

# Umbral para determinar ojo cerrado
EAR_UMBRAL = 0.22

# Tiempo continuo con ojos cerrados para activar alarma
TIEMPO_SOMNOLENCIA = 3.0


# ============================================================
# MEDIAPIPE
# ============================================================

mp_face_mesh = mp.solutions.face_mesh

face_mesh = mp_face_mesh.FaceMesh(
    max_num_faces=1,
    refine_landmarks=True,
    min_detection_confidence=0.5,
    min_tracking_confidence=0.5
)


# ============================================================
# CÁMARA
# ============================================================

camara = cv2.VideoCapture(CAMARA)

camara.set(
    cv2.CAP_PROP_FRAME_WIDTH,
    640
)

camara.set(
    cv2.CAP_PROP_FRAME_HEIGHT,
    480
)


if not camara.isOpened():

    print("ERROR: No se pudo abrir la cámara.")

    exit()


# ============================================================
# PUNTOS DE LOS OJOS
# ============================================================

OJO_IZQUIERDO = [
    33,
    160,
    158,
    133,
    153,
    144
]

OJO_DERECHO = [
    362,
    385,
    387,
    263,
    373,
    380
]


# ============================================================
# VARIABLES
# ============================================================

inicio_ojos_cerrados = None

alarma_activa = False

detener_alarma = False


# ============================================================
# CALCULAR EAR
# ============================================================

def calcular_EAR(puntos):

    A = np.linalg.norm(
        np.array(puntos[1]) -
        np.array(puntos[5])
    )

    B = np.linalg.norm(
        np.array(puntos[2]) -
        np.array(puntos[4])
    )

    C = np.linalg.norm(
        np.array(puntos[0]) -
        np.array(puntos[3])
    )

    if C == 0:
        return 0

    EAR = (A + B) / (2.0 * C)

    return EAR


# ============================================================
# ALARMA
# ============================================================

def alarma():

    global alarma_activa
    global detener_alarma

    while not detener_alarma:

        winsound.Beep(
            2000,
            400
        )

        time.sleep(0.1)

    alarma_activa = False


# ============================================================
# MENSAJE INICIAL
# ============================================================

print()
print("==============================================")
print("       DETECTOR DE SOMNOLENCIA")
print("==============================================")
print()
print("MediaPipe: ACTIVADO")
print("OpenCV: ACTIVADO")
print("Cámara: ACTIVADA")
print()
print("EAR menor a 0.22 = OJOS CERRADOS")
print("3 segundos = SOMNOLENCIA")
print()
print("Presione ESC para salir.")
print()
print("==============================================")


# ============================================================
# BUCLE PRINCIPAL
# ============================================================

while True:

    ret, frame = camara.read()

    if not ret:

        print("ERROR: No se pudo obtener imagen.")

        break


    # ========================================================
    # EFECTO ESPEJO
    # ========================================================

    frame = cv2.flip(
        frame,
        1
    )


    # ========================================================
    # CONVERTIR BGR A RGB
    # ========================================================

    rgb = cv2.cvtColor(
        frame,
        cv2.COLOR_BGR2RGB
    )


    # ========================================================
    # MEDIAPIPE
    # ========================================================

    resultado = face_mesh.process(rgb)
# ========================================================
    # ROSTRO DETECTADO
    # ========================================================

    if resultado.multi_face_landmarks:

        rostro = resultado.multi_face_landmarks[0]

        altura, ancho, _ = frame.shape


        # ====================================================
        # OJO IZQUIERDO
        # ====================================================

        puntos_izquierdo = []

        for indice in OJO_IZQUIERDO:

            punto = rostro.landmark[indice]

            x = int(
                punto.x * ancho
            )

            y = int(
                punto.y * altura
            )

            puntos_izquierdo.append(
                (x, y)
            )


        # ====================================================
        # OJO DERECHO
        # ====================================================

        puntos_derecho = []

        for indice in OJO_DERECHO:

            punto = rostro.landmark[indice]

            x = int(
                punto.x * ancho
            )

            y = int(
                punto.y * altura
            )

            puntos_derecho.append(
                (x, y)
            )


        # ====================================================
        # CALCULAR EAR
        # ====================================================

        ear_izquierdo = calcular_EAR(
            puntos_izquierdo
        )

        ear_derecho = calcular_EAR(
            puntos_derecho
        )


        # Promedio de ambos ojos

        ear = (
            ear_izquierdo +
            ear_derecho
        ) / 2


        # ====================================================
        # DIBUJAR PUNTOS
        # ====================================================

        for x, y in puntos_izquierdo:

            cv2.circle(
                frame,
                (x, y),
                2,
                (255, 0, 0),
                -1
            )


        for x, y in puntos_derecho:

            cv2.circle(
                frame,
                (x, y),
                2,
                (255, 0, 0),
                -1
            )


        # ====================================================
        # MOSTRAR EAR
        # ====================================================

        cv2.putText(
            frame,
            f"EAR: {ear:.3f}",
            (10, 35),
            cv2.FONT_HERSHEY_SIMPLEX,
            0.8,
            (255, 255, 0),
            2
        )


        # ====================================================
        # OJOS CERRADOS
        # ====================================================

        if ear < EAR_UMBRAL:

            if inicio_ojos_cerrados is None:

                inicio_ojos_cerrados = time.time()


            tiempo_cerrado = (
                time.time() -
                inicio_ojos_cerrados
            )


            cv2.putText(
                frame,
                f"OJOS CERRADOS: {tiempo_cerrado:.1f}s",
                (10, 70),
                cv2.FONT_HERSHEY_SIMPLEX,
                0.8,
                (0, 0, 255),
                2
            )


            # =================================================
            # SOMNOLENCIA
            # =================================================

            if tiempo_cerrado >= TIEMPO_SOMNOLENCIA:

                cv2.putText(
                    frame,
                    "!!! SOMNOLENCIA !!!",
                    (10, 120),
                    cv2.FONT_HERSHEY_SIMPLEX,
                    1,
                    (0, 0, 255),
                    3
                )


                cv2.putText(
                    frame,
                    "ALARMA ACTIVADA",
                    (10, 155),
                    cv2.FONT_HERSHEY_SIMPLEX,
                    0.8,
                    (0, 0, 255),
                    2
                )


                # Activar alarma

                if not alarma_activa:

                    alarma_activa = True
                    detener_alarma = False

                    hilo = threading.Thread(
                        target=alarma,
                        daemon=True
                    )

                    hilo.start()


        # ====================================================
        # OJOS ABIERTOS
        # ====================================================

        else:

            inicio_ojos_cerrados = None


            cv2.putText(
                frame,
                "OJOS ABIERTOS",
                (10, 70),
                cv2.FONT_HERSHEY_SIMPLEX,
                0.8,
                (0, 255, 0),
                2
            )


            # Apagar alarma

            if alarma_activa:

                detener_alarma = True

                alarma_activa = False


    # ========================================================
    # NO HAY ROSTRO
    # ========================================================

    else:

        inicio_ojos_cerrados = None


        cv2.putText(
            frame,
            "ROSTRO NO DETECTADO",
            (10, 40),
            cv2.FONT_HERSHEY_SIMPLEX,
            0.8,
            (0, 0, 255),
            2
        )


    # ========================================================
    # MOSTRAR CÁMARA
    # ========================================================

    cv2.imshow(
        "Detector de Somnolencia",
        frame
    )


    # ========================================================
    # SALIR CON ESC
    # ========================================================

    if cv2.waitKey(1) & 0xFF == 27:

        detener_alarma = True

        break


# ============================================================
# FINALIZAR
# ============================================================

camara.release()

cv2.destroyAllWindows()

print("Sistema finalizado.")