import json
import sys

import cv2


def main():
    if len(sys.argv) != 5:
        return 2

    model_path, image_path = sys.argv[1], sys.argv[2]
    minimum_sharpness, maximum_pose_asymmetry = float(sys.argv[3]), float(sys.argv[4])
    image = cv2.imread(image_path)
    if image is None:
        print(json.dumps({"error": "invalid_image"}))
        return 1

    height, width = image.shape[:2]
    detector = cv2.FaceDetectorYN.create(model_path, "", (width, height), 0.85, 0.3, 5000)
    _, detections = detector.detect(image)
    faces = 0 if detections is None else len(detections)
    result = {"faces": faces, "valid": False}
    if faces != 1:
        print(json.dumps(result))
        return 0

    face = detections[0]
    x, y, face_width, face_height = [int(value) for value in face[:4]]
    x = max(0, x)
    y = max(0, y)
    crop = image[y:min(height, y + face_height), x:min(width, x + face_width)]
    sharpness = cv2.Laplacian(cv2.cvtColor(crop, cv2.COLOR_BGR2GRAY), cv2.CV_64F).var()
    if sharpness < minimum_sharpness:
        print(json.dumps({**result, "reason": "blurred"}))
        return 0

    # YuNet landmarks: right eye, left eye, nose, right mouth, left mouth.
    right_eye = face[4:6]
    left_eye = face[6:8]
    nose = face[8:10]
    eye_center_x = (right_eye[0] + left_eye[0]) / 2
    eye_distance = abs(left_eye[0] - right_eye[0])
    pose_asymmetry = abs(nose[0] - eye_center_x) / max(eye_distance, 1)
    if pose_asymmetry > maximum_pose_asymmetry:
        print(json.dumps({**result, "reason": "side_face"}))
        return 0

    print(json.dumps({"faces": faces, "valid": True, "sharpness": sharpness}))
    return 0


if __name__ == "__main__":
    sys.exit(main())