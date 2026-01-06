<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AirLyft Booking</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <style>
    /* Base Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    html, body {
      height: 100%;
      width: 100%;
      scroll-behavior: smooth;
      overflow-x: hidden;
    }

    
    /* Welcome Container - Full screen background */
    .welcome-container {
            height: 100vh;
            /* More luxurious gradient background */
            background: linear-gradient(135deg, #1a2a4b, #2a4a7a, #4a6a9a); /* Darker, richer blues */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

   .welcome-box {
            background: rgba(255, 255, 255, 0.1); /* Lighter transparency */
            backdrop-filter: blur(15px); /* Stronger blur for frosted glass effect */
            -webkit-backdrop-filter: blur(15px); /* Safari support */
            padding: 50px; /* More padding for a premium feel */
            border-radius: 25px; /* More rounded corners */
            /* Deeper, more sophisticated shadow */
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2); /* Subtle light border */
        }

    .welcome-box h1 {
            font-family: 'Playfair Display', serif; /* Elegant font for main heading */
            font-size: 3.2rem; /* Larger and more impactful */
            margin-bottom: 15px; /* More space */
            font-weight: 700;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.4); /* Clearer text shadow */
        }

   .welcome-box p {
            margin: 15px 0; /* Increased margin */
            font-size: 1.25rem; /* Slightly larger text */
            font-weight: 300; /* Lighter weight for refinement */
        }
	
     .btn {
            margin-top: 30px; /* Increased margin-top for separation */
            padding: 15px 35px; /* Larger button */
            background: #f0c300; /* Gold accent color */
            color: #333; /* Dark text for contrast */
            border: none;
            border-radius: 50px;
            font-weight: 600; /* Bolder text */
            font-size: 18px; /* Larger font size */
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            display: inline-block;
            box-shadow: 0 5px 15px rgba(240, 195, 0, 0.4); /* Subtle shadow for depth */
            text-decoration: none; /* Remove underline from link */
        }

        .btn:hover {
            background: #e0b300; /* Slightly darker gold on hover */
            transform: translateY(-3px); /* Lift effect */
            box-shadow: 0 8px 20px rgba(240, 195, 0, 0.6); /* Enhanced shadow on hover */
        }
 @media (max-width: 768px) {
            .welcome-box {
                padding: 30px;
                margin: 20px; /* Ensure some margin on smaller screens */
            }
            .welcome-box h1 {
                font-size: 2.5rem;
            }
            .welcome-box p {
                font-size: 1rem;
            }
            .welcome-message {
                font-size: 0.9rem;
                margin-top: 20px;
            }
            .btn {
                padding: 12px 25px;
                font-size: 16px;
                margin-top: 20px;
            }
        }

        @media (max-width: 480px) {
            .welcome-box {
                padding: 25px;
            }
            .welcome-box h1 {
                font-size: 2rem;
                margin-bottom: 10px;
            }
            .welcome-box p {
                font-size: 0.9rem;
                margin: 10px 0;
            }
            .welcome-message {
                font-size: 0.85rem;
                margin-top: 15px;
            }
            .btn {
                padding: 10px 20px;
                font-size: 14px;
                margin-top: 15px;
            }
        }
  </style>
</head>
<body>
<body>
  <div class="welcome-container">
      <div class="welcome-box">
        <h1>Welcome to Airlyft</h1>
        <p>Air lift Online Booking</p>
        <div class="input-box welcome-message">
          <p>Ready to get started?</p>
        </div>
        <a class="btn" href="panel.php">Get Started</a>
      </div>
    </div>
  </div>
</body>



</body>
</html>
