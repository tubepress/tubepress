/**
 * @author Oliver Lillie (aka buggedcom) <publicmail@buggedcom.co.uk>
 *
 * @license BSD
 * @copyright Copyright (c) 2008 Oliver Lillie <http://www.buggedcom.co.uk>
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:  The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
*/
	private $_messages = array(
		
		'generic_temp_404' 								=> 'The temporary directory does not exist.',
		'generic_temp_writable' 						=> 'The temporary directory is not write-able by the web server.',
		
		'getFileInfo_no_input' 							=> 'Input file does not exist so no information can be retrieved.',
		'streamFLV_no_input' 							=> 'Input file has not been set so the FLV cannot be streamed.',
		'streamFLV_passed_eof' 							=> 'You have tried to stream to a point in the file that does not exit.',
		'setInputFile_file_existence' 					=> 'Input file "#file" does not exist',
		'extractAudio_valid_format' 					=> 'Value "#format" set from $toolkit->extractAudio, is not a valid audio format. Valid values ffmpeg self::FORMAT_AAC, PHPVideoToolkit::FORMAT_AIFF, PHPVideoToolkit::FORMAT_MP2, PHPVideoToolkit::FORMAT_MP3, PHPVideoToolkit::FORMAT_MP4, PHPVideoToolkit::FORMAT_MPEG4, PHPVideoToolkit::FORMAT_M4A or PHPVideoToolkit::FORMAT_WAV. If you wish to specifically try to set another format you should use the advanced function $toolkit->addCommand. Set $command to "-f" and $argument to your required value.',
		'extractFrame_video_frame_rate_404' 			=> 'You have attempted to extract a thumbnail from a video while automagically guessing the framerate of the video, but the framerate could not be accessed. You can remove this error by manually setting the frame rate of the video.',
		'extractFrame_video_info_404' 					=> 'You have attempted to extract a thumbnail from a video and check to see if the thumbnail exists, however it was not possible to access the video information. Please check your temporary directory permissions for read/write access by the webserver.',
		'extractFrame_video_frame_count' 				=> 'You have attempted to extract a thumbnail from a video but the thumbnail you are trying to extract does not exist in the video.',
		'extractFrames_video_begin_frame_count' 		=> 'You have attempted to extract thumbnails from a video but the thumbnail you are trying to start the extraction from does not exist in the video.',
		'extractFrames_video_end_frame_count' 			=> 'You have attempted to extract thumbnails from a video but the thumbnail you are trying to end the extraction at does not exist in the video.',
		'setFormat_valid_format' 						=> 'Value "#format" set from $toolkit->setFormat, is not a valid format. Valid values are PHPVideoToolkit::FORMAT_3GP2, PHPVideoToolkit::FORMAT_3GP, PHPVideoToolkit::FORMAT_AAC, PHPVideoToolkit::FORMAT_AIFF, PHPVideoToolkit::FORMAT_AMR, PHPVideoToolkit::FORMAT_ASF, PHPVideoToolkit::FORMAT_AVI, PHPVideoToolkit::FORMAT_FLV, PHPVideoToolkit::FORMAT_GIF, PHPVideoToolkit::FORMAT_MJ2, PHPVideoToolkit::FORMAT_MP2, PHPVideoToolkit::FORMAT_MP3, PHPVideoToolkit::FORMAT_MP4, PHPVideoToolkit::FORMAT_MPEG4, PHPVideoToolkit::FORMAT_M4A, PHPVideoToolkit::FORMAT_MPEG, PHPVideoToolkit::FORMAT_MPEG1, PHPVideoToolkit::FORMAT_MPEG2, PHPVideoToolkit::FORMAT_MPEGVIDEO, PHPVideoToolkit::FORMAT_PSP, PHPVideoToolkit::FORMAT_RM, PHPVideoToolkit::FORMAT_SWF, PHPVideoToolkit::FORMAT_VOB, PHPVideoToolkit::FORMAT_WAV, PHPVideoToolkit::FORMAT_JPG. If you wish to specifically try to set another format you should use the advanced function $toolkit->addCommand. Set $command to "-f" and $argument to your required value.',
		'setAudioSampleFrequency_valid_frequency' 		=> 'Value "#frequency" set from $toolkit->setAudioSampleFrequency, is not a valid integer. Valid values are 11025, 22050, 44100. If you wish to specifically try to set another frequency you should use the advanced function $toolkit->addCommand. Set $command to "-ar" and $argument to your required value.',
		'setAudioFormat_valid_format' 					=> 'Value "#format" set from $toolkit->setAudioFormat, is not a valid format. Valid values are PHPVideoToolkit::FORMAT_AAC, PHPVideoToolkit::FORMAT_AIFF, PHPVideoToolkit::FORMAT_AMR, PHPVideoToolkit::FORMAT_ASF, PHPVideoToolkit::FORMAT_MP2, PHPVideoToolkit::FORMAT_MP3, PHPVideoToolkit::FORMAT_MP4, PHPVideoToolkit::FORMAT_MPEG2, PHPVideoToolkit::FORMAT_RM, PHPVideoToolkit::FORMAT_WAV. If you wish to specifically try to set another format you should use the advanced function $toolkit->addCommand. Set $command to "-acodec" and $argument to your required value.',
		'setVideoFormat_valid_format' 					=> 'Value "#format" set from $toolkit->setAudioFormat, is not a valid format. Valid values are PHPVideoToolkit::FORMAT_3GP2, PHPVideoToolkit::FORMAT_3GP, PHPVideoToolkit::FORMAT_AVI, PHPVideoToolkit::FORMAT_FLV, PHPVideoToolkit::FORMAT_GIF, PHPVideoToolkit::FORMAT_MJ2, PHPVideoToolkit::FORMAT_MP4, PHPVideoToolkit::FORMAT_MPEG4, PHPVideoToolkit::FORMAT_M4A, PHPVideoToolkit::FORMAT_MPEG, PHPVideoToolkit::FORMAT_MPEG1, PHPVideoToolkit::FORMAT_MPEG2, PHPVideoToolkit::FORMAT_MPEGVIDEO. If you wish to specifically try to set another format you should use the advanced function $toolkit->addCommand. Set $command to "-vcodec" and $argument to your required value.',
		'setAudioBitRate_valid_bitrate' 				=> 'Value "#bitrate" set from $toolkit->setAudioBitRate, is not a valid integer. Valid values are 16, 32, 64, 128. If you wish to specifically try to set another bitrate you should use the advanced function $toolkit->addCommand. Set $command to "-ab" and $argument to your required value.',
		'prepareImagesForConversionToVideo_one_img' 	=> 'When compiling a movie from a series of images, you must include at least one image.',
		'prepareImagesForConversionToVideo_img_404' 	=> '"#img" does not exist.',
		'prepareImagesForConversionToVideo_img_copy' 	=> '"#img" can not be copied to "#tmpfile"',
		'prepareImagesForConversionToVideo_img_type' 	=> 'The images can not be prepared for conversion to video. Please make sure all images are of the same type, ie gif, png, jpeg and then try again.',
		'setVideoOutputDimensions_valid_format' 		=> 'Value "#format" set from $toolkit->setVideoOutputDimensions, is not a valid preset dimension. Valid values are PHPVideoToolkit::SIZE_SQCIF, PHPVideoToolkit::SIZE_SAS, PHPVideoToolkit::SIZE_QCIF, PHPVideoToolkit::SIZE_CIF, PHPVideoToolkit::SIZE_4CIF, PHPVideoToolkit::SIZE_QQVGA, PHPVideoToolkit::SIZE_QVGA, PHPVideoToolkit::SIZE_VGA, PHPVideoToolkit::SIZE_SVGA, PHPVideoToolkit::SIZE_XGA, PHPVideoToolkit::SIZE_UXGA, PHPVideoToolkit::SIZE_QXGA, PHPVideoToolkit::SIZE_SXGA, PHPVideoToolkit::SIZE_QSXGA, PHPVideoToolkit::SIZE_HSXGA, PHPVideoToolkit::SIZE_WVGA, PHPVideoToolkit::SIZE_WXGA, PHPVideoToolkit::SIZE_WSXGA, PHPVideoToolkit::SIZE_WUXGA, PHPVideoToolkit::SIZE_WOXGA, PHPVideoToolkit::SIZE_WQSXGA, PHPVideoToolkit::SIZE_WQUXGA, PHPVideoToolkit::SIZE_WHSXGA, PHPVideoToolkit::SIZE_WHUXGA, PHPVideoToolkit::SIZE_CGA, PHPVideoToolkit::SIZE_EGA, PHPVideoToolkit::SIZE_HD480, PHPVideoToolkit::SIZE_HD720, PHPVideoToolkit::SIZE_HD1080. You can also manually set the width and height.',
		'setVideoOutputDimensions_sas_dim' 				=> 'It was not possible to determine the input video dimensions so it was not possible to continue. If you wish to override this error please change the call to setVideoOutputDimensions and add a true argument to the arguments list... setVideoOutputDimensions(PHPVideoToolkit::SIZE_SAS, true);',
		'setVideoOutputDimensions_valid_integer' 		=> 'You tried to set the video output dimensions to an odd number. FFmpeg requires that the video output dimensions are of event value and divisible by 2. ie 2, 4, 6,... etc',
		'setVideoAspectRatio_valid_ratio' 				=> 'Value "#ratio" set from $toolkit->setVideoOutputDimensions, is not a valid preset dimension. Valid values are PHPVideoToolkit::RATIO_STANDARD, PHPVideoToolkit::RATIO_WIDE, PHPVideoToolkit::RATIO_CINEMATIC. If you wish to specifically try to set another video aspect ratio you should use the advanced function $toolkit->addCommand. Set $command to "-aspect" and $argument to your required value.',
		'addWatermark_img_404' 							=> 'Watermark file "#watermark" does not exist.',
		'addWatermark_vhook_disabled' 					=> 'Vhooking is not enabled in your FFmpeg binary. In order to allow video watermarking you must have FFmpeg compiled with --enable-vhook set. You can however watermark any extracted images using GD. To enable frame watermarking, call $toolkit->addGDWatermark($file) before you execute the extraction.',
		'addVideo_file_404' 							=> 'File "#file" does not exist.',
		'setOutput_output_dir_404' 						=> 'Output directory "#dir" does not exist!',
		'setOutput_output_dir_writable' 				=> 'Output directory "#dir" is not writable!',
		'setOutput_%_missing' 							=> 'The output of this command will be images yet you have not included the "%index" or "%timecode" in the $output_name.',
		'setOutput_%d_depreciated' 						=> 'The use of %d in the output file name is now depreciated. Please use %index. Number padding is still supported. You may also use %timecode instead to add a timecode to the filename.',
		'execute_input_404' 							=> 'Execute error. Input file missing.',
		'execute_output_not_set' 						=> 'Execute error. Output not set.',
		'execute_overwrite_process' 					=> 'Execute error. A file exists in the temp directory and is of the same name as this process file. It will conflict with this conversion. Conversion stopped.',
		'execute_overwrite_fail' 						=> 'Execute error. Output file exists. Process halted. If you wish to automatically overwrite files set the third argument in "PHPVideoToolkit::setOutput();" to "PHPVideoToolkit::OVERWRITE_EXISTING".',
		'execute_partial_error' 						=> 'Execute error. Output for file "#input" encountered a partial error. Files were generated, however one or more of them were empty.',
		'execute_image_error' 							=> 'Execute error. Output for file "#input" was not found. No images were generated.',
		'execute_output_404' 							=> 'Execute error. Output for file "#input" was not found. Please check server write permissions and/or available codecs compiled with FFmpeg. You can check the encode decode availability by inspecting the output array from PHPVideoToolkit::getFFmpegInfo().',
		'execute_output_empty' 							=> 'Execute error. Output for file "#input" was found, but the file contained no data. Please check the available codecs compiled with FFmpeg can support this type of conversion. You can check the encode decode availability by inspecting the output array from PHPVideoToolkit::getFFmpegInfo().',
		'execute_image_file_exists'						=> 'Execute error. There is a file name conflict. The file "#file" already exists in the filesystem. If you wish to automatically overwrite files set the third argument in "PHPVideoToolkit::setOutput();" to "PHPVideoToolkit::OVERWRITE_EXISTING".',
		'execute_result_ok_but_unwritable'				=> 'Process Partially Completed. The process successfully completed however it was not possible to output to "#output". The output was left in the temp directory "#process" for a manual file movement.',
		'execute_result_ok'								=> 'Process Completed. The process successfully completed. Output was generated to "#output".',
			
		'ffmpeg_log_ffmpeg_output'						=> 'OUTPUT',
		'ffmpeg_log_ffmpeg_result'						=> 'RESULT',
		'ffmpeg_log_ffmpeg_command'						=> 'COMMAND',
		'ffmpeg_log_ffmpeg_join_gunk'					=> 'FFMPEG JOIN OUTPUT',
		'ffmpeg_log_ffmpeg_gunk'						=> 'FFMPEG OUTPUT',
		'ffmpeg_log_separator'							=> '-------------------------------'
			
		);
